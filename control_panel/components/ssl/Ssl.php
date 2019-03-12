<?php

namespace control_panel\components\ssl;

use Yii;

use control_panel\libs\GoGetSSLApi;
use GoGetSSLAuthException;
use common\models\sommerces\ThirdPartyLog;

/**
 * Class Ssl
 * @package control_panel\components\ssl
 */
class Ssl {

    const DCV_METHOD_EMAIL = 'email';
    const DCV_METHOD_HTTP = 'http';
    const DCV_METHOD_HTTPS = 'https';
    const DCV_METHOD_DNS = 'dns';

    const SIGNATURE_HASH = '';

    /**
     * @var GoGetSSLApi
     */
    protected static $_instance;

    /**
     * Get instance api sdk
     * @return GoGetSSLApi
     */
    protected static function getInstance()
    {
        if (!static::$_instance) {
            static::$_instance = new GoGetSSLApi(!empty(Yii::$app->params['testSSL']));
            static::$_instance->auth(Yii::$app->params['goGetSSLUsername'], Yii::$app->params['goGetSSLPassword']);
        }

        return static::$_instance;
    }

    /**
     * Generate CSR
     * @param array $data
     * @return mixed
     */
    public static function generateCSR($data)
    {
        try {
            $result = static::getInstance()->generateCSR($data);
        } catch (GoGetSSLAuthException $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Order SSL
     * @param array $data
     * @return mixed
     */
    public static function addSSLOrder($data)
    {
        try {
            $result = static::getInstance()->addSSLOrder($data);
        } catch (GoGetSSLAuthException $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Renew SSL Order
     * @param array $data
     * @return mixed
     */
    public static function addSSLRenewOrder($data)
    {
        try {
            $result = static::getInstance()->addSSLRenewOrder($data);
        } catch (GoGetSSLAuthException $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Get order status
     * @param $orderId
     * @return null
     */
    public static function getOrderStatus($orderId)
    {
        try {
            $result = static::getInstance()->getOrderStatus($orderId);
        } catch (GoGetSSLAuthException $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Get total orders
     * @return null
     */
    public static function getTotalOrders()
    {
        try {
            $result = static::getInstance()->getTotalOrders();
        } catch (GoGetSSLAuthException $e) {
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * Get send details
     * @return array
     */
    public static function getSendDetails()
    {
        return [
            'url' => static::getInstance()->getLastUrl(),
            'data' => static::getInstance()->getLastData()
        ];
    }

    /**
     * Get response details
     * @return array
     */
    public static function getResponseDetails()
    {
        return [
            'status' => static::getInstance()->getLastStatus(),
            'response' => static::getInstance()->getLastResult(), // This is response, before json_decode
            'result' => static::getInstance()->getLastResponse() // This is response, after json_decode
        ];
    }
}