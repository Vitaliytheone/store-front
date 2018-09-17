<?php

namespace my\helpers;

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

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, (int)!empty($post));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']);
        }

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
}