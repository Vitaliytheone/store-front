<?php

namespace common\helpers;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class ApiProviders
 * @package common\helpers
 */
class ApiProviders
{

    const COMMON_API_URL_TPL = 'https://{{api_host}}/api/v2';
    const API_RESPONSE_ERROR_FIELD = 'error';

    public $api_url;
    public $api_key;

    /**
     * ApiProviders constructor.
     * @param $site
     * @param $apiKey
     */
    public function __construct($site, $apiKey)
    {
        $this->api_url = str_replace('{{api_host}}', $site, self::COMMON_API_URL_TPL);
        $this->api_key = $apiKey;
    }

    /**
     * Add new order
     * @param $data
     * @return mixed
     */
    public function order($data) {
        $post = array_merge(array('key' => $this->api_key, 'action' => 'add'), $data);
        return json_decode($this->connect($post));
    }

    /**
     * Get order status
     * @param $order_id
     * @return mixed
     */
    public function status($order_id) {
        return json_decode($this->connect(array(
            'key' => $this->api_key,
            'action' => 'status',
            'id' => $order_id
        )));
    }

    /**
     * Get services
     * @param array $serviceTypeFilter array of service`s type filters
     * @return array|mixed
     */
    public function services($serviceTypeFilter = null) {
        $services = json_decode($this->connect(array(
            'key' => $this->api_key,
            'action' => 'services',
        )), true);

         //Filtering results by service type
        if (is_array($services) && is_array($serviceTypeFilter)) {
            $filteredServices = array_filter($services, function($service, $index) use ($serviceTypeFilter){
                return in_array(ArrayHelper::getValue($service, 'type', null), $serviceTypeFilter);
            }, ARRAY_FILTER_USE_BOTH);
            return  $filteredServices;
        }
        // Or return all services
        return $services;
    }

    /**
     * Get balance
     * @return mixed
     */
    public function balance() {
        return json_decode($this->connect(array(
            'key' => $this->api_key,
            'action' => 'balance',
        )));
    }

    /**
     * Connection
     * @param $post
     * @param bool $json
     * @return mixed
     * @throws Exception
     */
    private function connect($post, $json = false) {
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        $result = curl_exec($ch);

        // System errors
        if (curl_errno($ch) && empty($result)) {
            curl_close($ch);
            throw new Exception();
        }
        curl_close($ch);

        // Api errors
        $jsonResult = json_decode($result,true);

        if(json_last_error()){
            throw new Exception('API response JSON decode errors! ' . json_last_error_msg() . '(' . json_last_error() . ')');
        }
        if (isset($jsonResult[self::API_RESPONSE_ERROR_FIELD])) {
            throw new Exception('API response errors: '.$jsonResult[self::API_RESPONSE_ERROR_FIELD]);
        }

        if ($json) {
            return $jsonResult;
        }
        return $result;
    }
}