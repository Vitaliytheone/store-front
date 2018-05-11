<?php
namespace my\helpers\order;

use my\components\ddos\Ddos;
use my\components\ssl\Ssl;
use my\helpers\CurlHelper;
use common\models\panels\Orders;
use common\models\panels\SslCert;
use common\models\panels\ThirdPartyLog;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class OrderSslHelper
 * @package my\helpers
 */
class OrderSslHelper {

    /**
     * Add config
     * @param SslCert $ssl
     * @param array $data
     * @return boolean
     */
    public static function addConfig($ssl, $data = [])
    {
        $data['key'] = Yii::$app->params['system.sslScriptKey'];
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, $data, 'cron.ssl_status.send_ssl_config');

        $result = CurlHelper::request(Yii::$app->params['system.sslScriptUrl'], $data);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, $result, 'cron.ssl_status.ssl_config');

        if ($result && 'ok' == strtolower($result)) {
            return true;
        }

        return false;
    }

    /**
     * Generate ssl CSR uses order
     * @param Orders $order
     * @return mixed
     */
    public static function generateCSR(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $details = ArrayHelper::getValue($orderDetails, 'details');

        $organization = ArrayHelper::getValue($details, 'admin_organization');
        $organization = trim($organization);
        if (empty($organization)) {
            $organization = $details['admin_firstname'] . ' ' . $details['admin_lastname'];
        }

        $data = [
            'csr_commonname' => $order->domain,
            'csr_organization' => $organization,
            'csr_department' => 'IT Department',
            'csr_city' => $details['admin_city'],
            'csr_state' => $details['admin_region'],
            'csr_country' => $details['admin_country'],
            'csr_email' => $details['admin_email'],
        ];



        // Generate ssl csr code
        $csr = Ssl::generateCSR($data);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, Ssl::getSendDetails(), 'cron.ssl.send_csr_generator');
        ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, Ssl::getResponseDetails(), 'cron.ssl.csr_generator');

        return $csr;
    }

    /**
     * Order SSL
     * @param Orders $order
     * @param array $data
     * @return mixed
     */
    public static function addSSLOrder(Orders $order, $data = [])
    {
        $orderDetails = $order->getDetails();
        $details = ArrayHelper::getValue($orderDetails, 'details');

        $organization = ArrayHelper::getValue($details, 'admin_organization');
        $organization = trim($organization);
        if (empty($organization)) {
            $organization = $details['admin_firstname'] . ' ' . $details['admin_lastname'];
        }

        $data = array_merge($details, [
            'admin_organization' => $organization,
            'admin_title' => $details['admin_job'],
            'tech_organization' => $organization,
            'tech_firstname' => $details['admin_firstname'],
            'tech_lastname' => $details['admin_lastname'],
            'tech_addressline1' => $details['admin_addressline1'],
            'tech_phone' => $details['admin_phone'],
            'tech_title' => $details['admin_job'],
            'tech_email' => $details['admin_email'],
            'tech_city' => $details['admin_city'],
            'tech_country' => $details['admin_country'],
            'tech_postalcode' => $details['admin_postalcode'],
            'tech_region' => $details['admin_region'],
            'period' => SslCert::SSL_CERT_PERIOD,
            'server_count' => '-1',
            'webserver_type' => 1,
            'dcv_method' => Ssl::DCV_METHOD_HTTP,
            'signature_hash' => 'SHA2',
        ], $data);

        // Add ssl order
        $orderSsl = Ssl::addSSLOrder($data);
        ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, Ssl::getSendDetails(), 'cron.ssl.send_order_ssl');
        ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, Ssl::getResponseDetails(), 'cron.ssl.order_ssl');

        if (null != $orderSsl) {
            return $orderSsl;
        }

        // Add ssl order
        $orderSsl = Ssl::addSSLOrder($data);
        ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, Ssl::getSendDetails(), 'cron.ssl.send_order_ssl');
        ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, Ssl::getResponseDetails(), 'cron.ssl.order_ssl');

        return $orderSsl;
    }

    /**
     * @param Orders $order
     * @param SslCert $ssl
     * @return mixed
     * @throws Exception
     */
    public static function addSslRenewOrder(Orders $order, SslCert $ssl)
    {
        if (!$ssl instanceof SslCert) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, 'SslCert is undefined!', 'cron.ssl.send_order_renew_ssl');

            throw new Exception('SslCert is undefined!');
        }

        if ($ssl->status != SslCert::STATUS_ACTIVE  || $ssl->checked != SslCert::CHECKED_YES) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, 'Invalid SslCert!', 'cron.ssl.send_order_renew_ssl');

            throw new Exception('Invalid SslCert!');
        }

        $sslOrderDetails = $ssl->getOrderDetails();
        $sslOrderStatus = $ssl->getOrderStatusDetails();

        if (empty($sslOrderDetails) || empty($sslOrderStatus)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, 'Invalid SslCert data!', 'cron.ssl.send_order_renew_ssl');

            throw new Exception('Invalid SslCert data!');
        }

        $data = [
            'product_id' => $sslOrderDetails['product_id'],
            'csr' => $ssl->csr_code,
            'admin_email' => $sslOrderStatus['admin_email'],
            'admin_firstname' => $sslOrderStatus['admin_firstname'],
            'admin_lastname' => $sslOrderStatus['admin_lastname'],
            'admin_title' => $sslOrderStatus['admin_title'],
            'admin_phone' => $sslOrderStatus['admin_phone'],
            'admin_addressline1' => $sslOrderStatus['admin_title'],
            'admin_organization' => ArrayHelper::getValue($sslOrderStatus,'admin_organization'),
            'admin_city' => ArrayHelper::getValue($sslOrderStatus, 'admin_city'),
            'admin_country' => ArrayHelper::getValue($sslOrderStatus, 'admin_country'),
            'admin_fax' => ArrayHelper::getValue($sslOrderStatus, 'admin_fax'),
            'admin_postalcode' => ArrayHelper::getValue($sslOrderStatus, 'admin_postalcode'),
            'admin_region' => ArrayHelper::getValue($sslOrderStatus, 'admin_region'),

            'tech_organization' => $sslOrderStatus['tech_organization'],
            'tech_firstname' => $sslOrderStatus['tech_firstname'],
            'tech_lastname' => $sslOrderStatus['tech_lastname'],
            'tech_addressline1' => $sslOrderStatus['tech_addressline1'],
            'tech_phone' => $sslOrderStatus['tech_phone'],
            'tech_title' => $sslOrderStatus['tech_title'],
            'tech_email' => $sslOrderStatus['tech_email'],
            'tech_city' => $sslOrderStatus['tech_city'],
            'tech_country' => $sslOrderStatus['tech_country'],
            'tech_postalcode' => $sslOrderStatus['tech_postalcode'],
            'tech_region' => $sslOrderStatus['tech_region'],
            'period' => SslCert::SSL_CERT_PERIOD,
            'server_count' => '-1',
            'webserver_type' => 1,
            'dcv_method' => Ssl::DCV_METHOD_HTTP,
            'signature_hash' => 'SHA2',
        ];

        $orderRenewSsl = Ssl::addSSLRenewOrder($data);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, Ssl::getSendDetails(), 'cron.ssl.send_order_renew_ssl');
        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, Ssl::getResponseDetails(), 'cron.ssl.order_renew_ssl');

        return $orderRenewSsl;
    }

    /**
     * Get order status
     * @param SslCert $ssl
     * @return mixed
     */
    public static function getOrderStatus(SslCert $ssl)
    {
        $order = $ssl->getOrderDetails();
        $orderId = ArrayHelper::getValue($order, 'order_id');

        $orderDetails = Ssl::getOrderStatus($orderId);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, Ssl::getSendDetails(), 'cron.ssl_status.send_get_status');
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, Ssl::getResponseDetails(), 'cron.ssl_status.get_status');

        return $orderDetails;
    }

    /**
     * Add to ddos guard service
     * @param SslCert $ssl
     * @param array $data
     * @return bool
     */
    public static function addDdos(SslCert $ssl, $data)
    {
        $data = array_merge(Yii::$app->params['ddosGuardOptions'], $data);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, $data, 'cron.ssl_status.send_ddos');

        // $crt + $ca code
        $returnResult = Ddos::add($data, $result);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, $result, 'cron.ssl_status.ddos');

        return $returnResult;
    }
}