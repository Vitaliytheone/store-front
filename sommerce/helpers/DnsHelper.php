<?php
namespace sommerce\helpers;

use Yii;
use common\components\dns\Dns;
use common\models\panels\ThirdPartyLog;
use common\models\stores\Stores;

/**
 * Class DnsHelper
 * @package sommerce\helpers
 */
class DnsHelper {

    /**
     * Add main domain dns
     * @param Stores $store
     */
    public static function addMainDns(Stores $store)
    {
        $result = true;
        $domain = $store->domain;

        $results = [];

        // If domain exist, return true
        if (Dns::getZoneInfo($domain, $results)) {
            return true;
        }

        // Add master
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'zone-type' => 'master',
            'ns' => implode(",", Yii::$app->params['ahnames.sommerce.ns']),
        ], 'store.send_master_dns');
        // Add master
        if (!Dns::addMaster($domain, Yii::$app->params['ahnames.sommerce.ns'], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $results, 'store.master_dns');


        // Add dns record type A
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'A',
            'record' => Yii::$app->params['dnsIp']
        ], 'store.send_dns_record_a');

        // Add dns record type A
        if (!Dns::addRecord($domain, '', [
            'record-type' => 'A',
            'record' => Yii::$app->params['dnsIp']
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $results, 'store.dns_record_a');


        // Add dns record type CNAME for www
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'CNAME',
            'record' => $domain
        ], 'store.send_dns_record_www_cname');

        // Add dns record type CNAME for www
        if (!Dns::addRecord($domain, 'www', [
            'record-type' => 'CNAME',
            'record' => $domain
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $results, 'store.dns_record_www_cname');

        return $result;
    }

    /**
     * Add subdomain dns
     * @param Stores $store
     */
    public static function addSubDns(Stores $store)
    {
        $result = true;
        $domain = $store->domain;
        $subPrefix = str_replace('.', '-', $domain);
        $storeDomainName = Yii::$app->params['storeDomain'];

        $results = [];

        // If sub domain exist, return true
        if (Dns::getRecordInfo($storeDomainName, $subPrefix, [
            'type' => 'CNAME'
        ], $results)) {
            return true;
        }

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'CNAME',
            'record' => $storeDomainName
        ], 'store.send_dns_record_cname');

        // Add NS type CNAME
        if (!Dns::addRecord($storeDomainName, $subPrefix, [
            'record-type' => 'CNAME',
            'record' => $storeDomainName
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $results, 'store.dns_record_cname');

        return $result;
    }

    /**
     * Remove main domain dns
     * @param Stores $store
     */
    public static function removeMainDns(Stores $store)
    {
        $result = true;
        $domain = $store->domain;

        // Remove all dns records
        if (!Dns::removeMaster($domain, $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $results, 'store.remove_master');

        return $result;
    }

    /**
     * Remove subdomain dns
     * @param Stores $store
     */
    public static function removeSubDns(Stores $store)
    {
        $result = true;
        $domain = $store->domain;
        $subPrefix = str_replace('.', '-', $domain);
        $storeDomainName = Yii::$app->params['storeDomain'];

        if (!Dns::removeRecord($storeDomainName, $subPrefix, [
            'type' => 'CNAME'
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $results, 'store.remove_record');

        return $result;
    }


    /**
     * Add dns
     * @param Stores $store
     */
    public static function addDns(Stores $store)
    {
        $result = static::addMainDns($store);

        return $result;
    }

    /**
     * Remove dns
     * @param Stores $store
     */
    public static function removeDns(Stores $store)
    {
        $result = static::removeMainDns($store);

        return $result;
    }
}