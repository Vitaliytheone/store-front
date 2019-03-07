<?php

namespace my\helpers\order;

use common\components\domains\Domain;
use common\models\panels\Domains;
use my\helpers\DomainsHelper;
use Yii;
use common\models\panels\Orders;
use common\models\panels\ThirdPartyLog;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class OrderDomainHelper
 * @package my\helpers\order
 */
class OrderDomainHelper
{

    /**
     * Create order contact
     * @param Orders $order
     * @return mixed
     * @throws \yii\base\UnknownClassException
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

        $registrar = static::getRegistrarClass($order);
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge($data, $registrar::getDefaultOptions()), 'cron.order.send_domain_contact');

        $contactResult = $registrar::contactCreate($data);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $contactResult, 'cron.order.domain_contact');

        return $contactResult;
    }

    /**
     * Register domain
     * @param Orders $order
     * @return array
     * @throws \yii\base\UnknownClassException
     */
    public static function domainRegister(Orders $order): array
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        $contactResult = ArrayHelper::getValue($orderDetails, 'domain_contact');
        $contactId = ArrayHelper::getValue($contactResult, 'id');
        $period = 1;
        $registrar = static::getRegistrarClass($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge([
            'domain' => $domain,
            'period' => $period,
            'registrant' => $contactId,
            'admin' => $contactId,
            'tech' => $contactId,
            'billing' => $contactId,
            ], $registrar::getDefaultOptions()), 'cron.order.send_domain_register');

        $domainResult = $registrar::domainRegister($domain, $contactId, $period);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $domainResult, 'cron.order.domain_register');

        return $domainResult;
    }

    /**
     * Get domain info
     * @param Orders $order
     * @return mixed
     * @throws yii\base\UnknownClassException
     */
    public static function domainGetInfo(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        $registrar = static::getRegistrarClass($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge([
            'domain' => $domain,
            ], $registrar::getDefaultOptions()), 'cron.order.send_domain_info');

        $domainInfoResult = $registrar::domainGetInfo($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $domainInfoResult, 'cron.order.domain_info');

        return $domainInfoResult;
    }

    /**
     * Enable Whois Protect
     * @param Orders $order
     * @return mixed
     * @throws yii\base\UnknownClassException
     */
    public static function domainEnableWhoisProtect(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        $registrar = static::getRegistrarClass($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge([
            'domain' => $domain,
        ], $registrar::getDefaultOptions()), 'cron.order.send_domain_assign');

        $assignResult = $registrar::domainEnableWhoisProtect($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $assignResult, 'cron.order.domain_assign');

        return $assignResult;
    }

    /**
     * Domain Set NSs
     * @param Orders $order
     * @return mixed
     * @throws yii\base\UnknownClassException
     */
    public static function domainSetNSs(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        $registrar = static::getRegistrarClass($order);
        $registrarName = static::getRegistrarName($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge([
            'domain' => $domain,
            'nss' => implode(',', array_filter(Yii::$app->params["{$registrarName}.my.ns"]))
            ], $registrar::getDefaultOptions()), 'cron.order.send_domain_nss');

        $setNss = $registrar::domainSetNSs($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $setNss, 'cron.order.domain_nss');

        return $setNss;
    }

    /**
     * Domain Enable lock
     * @param Orders $order
     * @return mixed
     * @throws yii\base\UnknownClassException
     */
    public static function domainEnableLock(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        $registrar = static::getRegistrarClass($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, array_merge([
            'domain' => $domain,
        ], $registrar::getDefaultOptions()), 'cron.order.send_domain_lock');

        $enableLock = $registrar::domainEnableLock($domain);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $enableLock, 'cron.order.domain_lock');

        return $enableLock;
    }

    /**
     * Domain renew registration
     * @param Orders $order
     * @return array
     * @throws Exception
     * @throws yii\base\UnknownClassException
     */
    public static function domainRenew(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');

        $domainInfoResult = static::domainGetInfo($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, $domainInfoResult, 'cron.prolong.domain_info_result');

        if (empty($domainInfoResult) || !empty($domainInfoResult['_error'])) {
            throw new Exception("Domain [$order->item_id] domainGetInfo returned an incorrect result!");
        }

        $expiryDateTime = ArrayHelper::getValue($domainInfoResult, 'expires');

        $expiry = date('Y-m-d', strtotime($expiryDateTime));

        if (empty($expiry)) {
            throw new Exception("Domain [$order->item_id] `expiry` info is not defined!");
        }

        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, [
            'domain' => $domain,
            'expiry' => $expiry
        ], 'cron.prolong.send_renew_domain');

        $registrar = static::getRegistrarClass($order);
        $domainRenewResult = $registrar::domainRenew($domain, $expiry);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, $domainRenewResult, 'cron.prolong.renew_domain');

        if (empty($domainRenewResult) || !empty($domainRenewResult['_error'])) {
            throw new Exception("Domain [$order->item_id] ! domainRenew return error!");
        }

        return $domainRenewResult;
    }

    /**
     * @param Orders $order
     * @return \common\components\domains\BaseDomain
     * @throws \yii\base\UnknownClassException
     */
    protected static function getRegistrarClass(Orders $order)
    {
        if (Orders::ITEM_PROLONGATION_DOMAIN === $order->item) {
            $domain = Domains::findOne($order->item_id);
            if (!empty($domain)) {
                return Domain::createRegistrarClass($domain->registrar);
            }
        }

        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        return DomainsHelper::getRegistrarClass($domain);
    }

    /**
     * @param Orders $order
     * @return string
     */
    protected static function getRegistrarName(Orders $order)
    {
        if (Orders::ITEM_PROLONGATION_DOMAIN === $order->item) {
            $domain = Domains::findOne($order->item_id);
            if (!empty($domain)) {
                return $domain->registrar;
            }
        }

        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        return DomainsHelper::getRegistrarName($domain);
    }
}