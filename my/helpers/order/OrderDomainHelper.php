<?php
namespace my\helpers\order;

use Yii;
use my\components\domains\Ahnames;
use common\models\panels\Orders;
use common\models\panels\ThirdPartyLog;
use yii\helpers\ArrayHelper;

/**
 * Class OrderDomainHelper
 * @package my\helpers\order
 */
class OrderDomainHelper {

    /**
     * Create order contact
     * @param Orders $order
     * @return mixed
     */
    public static function contactCreate(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $details = ArrayHelper::getValue($orderDetails, 'details', []);

        $data = [
            'email' => ArrayHelper::getValue($details, 'domain_email'),
            'first_name' => ArrayHelper::getValue($details, 'domain_firstname'),
            'last_name' => ArrayHelper::getValue($details, 'domain_lastname'),
            'organization' => ArrayHelper::getValue($details, 'domain_company'),
            'street1' => ArrayHelper::getValue($details, 'domain_address'),
            'city' => ArrayHelper::getValue($details, 'domain_city'),
            'province' => ArrayHelper::getValue($details, 'domain_state'),
            'postal_code' => ArrayHelper::getValue($details, 'domain_postalcode'),
            'country' => ArrayHelper::getValue($details, 'domain_country'),
            'voice_phone' => ArrayHelper::getValue($details, 'domain_phone'),
            'fax_phone' => ArrayHelper::getValue($details, 'domain_fax'),
        ];

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge($data, [
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ]), 'cron.order.send_domain_contact');

        $contactResult = Ahnames::contactCreate($data);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $contactResult, 'cron.order.domain_contact');

        return $contactResult;
    }

    /**
     * Register domain
     * @param Orders $order
     * @return array
     */
    public static function domainRegister(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        $contactResult = ArrayHelper::getValue($orderDetails, 'domain_contact');
        $contactId = ArrayHelper::getValue($contactResult, 'id');
        $period = 1;

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, [
            'domain' => $domain,
            'period' => $period,
            'registrant' => $contactId,
            'admin' => $contactId,
            'tech' => $contactId,
            'billing' => $contactId,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ], 'cron.order.send_domain_register');

        $domainResult = Ahnames::domainRegister($domain, $contactId, $period);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $domainResult, 'cron.order.domain_register');

        return $domainResult;
    }

    /**
     * Get domain info
     * @param Orders $order
     * @return mixed
     */
    public static function domainGetInfo(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ], 'cron.order.send_domain_info');

        $domainInfoResult = Ahnames::domainGetInfo($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $domainInfoResult, 'cron.order.domain_info');

        return $domainInfoResult;
    }

    /**
     * Enable Whois Protect
     * @param Orders $order
     * @return mixed
     */
    public static function domainEnableWhoisProtect(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ], 'cron.order.send_domain_assign');

        $assignResult = Ahnames::domainEnableWhoisProtect($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $assignResult, 'cron.order.domain_assign');

        return $assignResult;
    }

    /**
     * Domain Set NSs
     * @param Orders $order
     * @return mixed
     */
    public static function domainSetNSs(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
            'nss' => implode(",", array_filter(Yii::$app->params['ahnames.ns']))
        ], 'cron.order.send_domain_nss');

        $setNss = Ahnames::domainSetNSs($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $setNss, 'cron.order.domain_nss');

        return $setNss;
    }

    /**
     * Domain Enable lock
     * @param Orders $order
     * @return mixed
     */
    public static function domainEnableLock(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ], 'cron.order.send_domain_lock');

        $enableLock = Ahnames::domainEnableLock($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $enableLock, 'cron.order.domain_lock');

        return $enableLock;
    }
}