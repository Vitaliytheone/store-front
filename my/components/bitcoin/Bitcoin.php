<?php
namespace my\components\bitcoin;

/**
 * Class Bitcoin
 * @package my\components\bitcoin
 */
class Bitcoin {

    const METHOD_POST = 'POST';

    const METHOD_GET = 'GET';

    const ACTION = 'https://gateway.gear.mycelium.com';

    /**
     * Create order
     * @param string $bitcoinId
     * @param string $secret
     * @param array $params
     * @return mixed
     */
    public static function order($bitcoinId, $secret, $params = [])
    {
        $data = [
            'request_uri' => "/gateways/" . $bitcoinId . "/orders",
            'request_method' => static::METHOD_POST,
            'secret' => $secret,
            'params' => $params
        ];

        return static::request($data, $result);
    }

    /**
     * Generate signature
     * @param string $request_uri
     * @param string $secret
     * @return string
     */
    public static function generateSignature($request_uri, $secret)
    {
        $nonce = NULL;
        $body = NULL;

        $constant_digest = hash('sha512', $nonce . $body, TRUE);
        $payload = $_SERVER['REQUEST_METHOD'] . $request_uri . $constant_digest;
        $raw_signature = hash_hmac('sha512', $payload, $secret, TRUE);
        return base64_encode($raw_signature);
    }

    /**
     * Get header options
     * @param string $url
     * @param string $secret
     * @param string $params
     * @param bool $isPost
     * @return array
     */
    public static function getHeaderOptions($url, $secret, $params, $isPost = true)
    {
        if (is_array($params)) {
            $params = '?' . http_build_query($params);
        } else {
            $params = trim($params, '/');

            if (!empty($params)) {
                $params = "/" . $params;
            }
        }

        $nonce = (int)round(microtime(true) * 1000);
        $body = '';

        $nonce_hash = hash('sha512', (string) $nonce . $body, TRUE);
        $payload = ($isPost ? static::METHOD_POST : static::METHOD_GET) . $url . $params . $nonce_hash;
        $raw_signature = hash_hmac('sha512', $payload, $secret, TRUE);
        $signature = base64_encode($raw_signature);

        return [
            'Content-Type: application/json',
            'X-Nonce: ' . $nonce,
            'X-Signature: ' . $signature
        ];
    }

    /**
     * Request
     * @param array $data
     * @param array $result
     * @return mixed
     */
    public static function request($data, &$result) {
        $ch = curl_init();

        $url = (false === strpos($data['request_uri'],  'http')) ? static::ACTION . $data['request_uri'] :  $data['request_uri'];
        $isPost = $data['request_method'] == static::METHOD_POST;
        $params = !is_array($data['params']) ? "/" . ltrim($data['params'], '/') : '?' . http_build_query($data['params']);
        $headers = static::getHeaderOptions($data['request_uri'], $data['secret'], $params, $isPost);

        $curlData = [
            CURLOPT_URL             => $url . $params,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_SSL_VERIFYPEER  => TRUE,
            CURLOPT_CONNECTTIMEOUT  => 60,
            CURLOPT_POST            => $isPost
        ];

        curl_setopt_array($ch, $curlData);

        if (!$result = curl_exec($ch)) {
            return false;
        } elseif (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            return false;
        } else {
            return json_decode($result);
        }
    }
}