<?php

namespace common\helpers;

use Yii;
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

    const API_RESPONSE_KEY_ERROR = 'Invalid API key';

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
        return $this->connect($post);
    }

    /**
     * Get order status
     * @param $order_id
     * @return mixed
     */
    public function status($order_id) {
        return $this->connect(array(
            'key' => $this->api_key,
            'action' => 'status',
            'id' => $order_id
        ));
    }

    /**
     * Get services
     * @param array $serviceTypeFilter array of service`s type filters
     * @return array|mixed
     */
    public function services($serviceTypeFilter = null) {
        $services = $this->connect(array(
            'key' => $this->api_key,
            'action' => 'services',
        ));

        if (isset($services['error'])) {
            return $services;
        }

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
        return $this->connect(array(
            'key' => $this->api_key,
            'action' => 'balance',
        ));
    }

    /**
     * Connection
     * @param $post
     * @return mixed
     * @throws Exception
     */
    private function connect($post) {
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

        $jsonResult = json_decode($result,true);

        // Json decode errors
        if(json_last_error()){
            return [
                'error' => true,
                'message' => Yii::t('admin', 'products.message_api_json_decode_error'),
            ];
        }

        // Api errors
        $error = ArrayHelper::getValue($jsonResult, self::API_RESPONSE_ERROR_FIELD, null);
        if ($error) {
            if ($error === self::API_RESPONSE_KEY_ERROR) {
                $message = Yii::t('admin', 'products.message_api_key_error');
            } else {
                $message = Yii::t('admin', 'products.message_api_error');
            }

            return [
                'error' => true,
                'message' => $message,
            ];
        };

        return $jsonResult;
    }
}