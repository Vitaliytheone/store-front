<?php
namespace common\helpers;

use yii\helpers\ArrayHelper;

/**
 * Class CurlHelper
 * @package common\helpers
 */
class CurlHelper {

    /**
     * Curl request
     * @param $url
     * @param string $post
     * @param string $headers
     * @param string $userAgent
     * @return bool|mixed
     */
    public static function request($url, $post = '', $headers = '', $userAgent = '') {
        $_post = [];

        if (is_array($post)) {
            foreach ($post as $name => $value) {
                $_post[] = $name . '=' . urlencode($value);
            }
        }

        $ch = static::curlInit($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, (int)!empty($post));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        } else if (is_string($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($userAgent != '') {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }

        $result = curl_exec($ch);

        if (curl_errno($ch) != 0 && empty($result)) {
            $result = false;
        }

        curl_close($ch);

        return $result;
    }

    /**
     * Curl init
     * @param null|string $url
     * @param array $params
     * @return resource
     */
    public static function curlInit($url = null, $params = [])
    {
        $ch = curl_init($url);

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']);

            if (!empty($params['proxy_tunnel'])) {
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            }
        }

        return $ch;
    }

    /**
     * Get content
     * @param null|string $url
     * @param null|mixed $flags
     * @param null|mixed $context
     * @return string
     */
    public static function getContent($url, $flags = null, $context = null)
    {
        if (!empty(PROXY_CONFIG['main']['ip'])) {
            $context = ArrayHelper::merge((array)$context, [
                'http' => [
                    'proxy' => PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port'],
                    'request_fulluri' => true,
                ],
            ]);

            $flags = false;
        }

        $context = !empty($context) ? stream_context_create($context) : null;

        return @file_get_contents($url, $flags, $context);
    }
}