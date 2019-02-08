<?php

namespace common\helpers;


use common\models\gateways\Sites;
use my\helpers\DomainsHelper;
use Yii;
use common\components\dns\Dns;
use common\models\panels\ThirdPartyLog;
use common\models\stores\Stores;
use common\models\panels\Project;

/**
 * Class DnsHelper
 * @package sommerce\helpers
 */
class DnsHelper
{
    /**
     * @param Stores|Project|Sites $project
     * @return bool
     */
    public static function addMainDns($project)
    {
        $params = static::getProjectParams($project);

        if (!$params) {
            return false;
        }
        $logCodes = $params['logCodes'];
        $result = true;
        $results = [];

        // If domain exist, return true
        if (Dns::getZoneInfo($params['domain'], $results)) {
            return true;
        }

        // Add master
        ThirdPartyLog::log($params['logItem'], $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $params['domain'],
            'zone-type' => 'master',
            'ns' => implode(",", $params['registrarParams']),
        ], $logCodes['send_master_dns']);
        // Add master
        if (!Dns::addMaster($params['domain'], $params['registrarParams'], $results)) {
            $result = false;
        }
        ThirdPartyLog::log($params['logItem'], $project->id, $results, $logCodes['master_dns']);


        // Add dns record type A
        ThirdPartyLog::log($params['logItem'], $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $params['domain'],
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'A',
            'record' => Yii::$app->params['dnsIp']
        ], $logCodes['send_dns_record_a']);

        // Add dns record type A
        if (!Dns::addRecord($params['domain'], '', [
            'record-type' => 'A',
            'record' => Yii::$app->params['dnsIp']
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log($params['logItem'], $project->id, $results, $logCodes['dns_record_a']);


        // Add dns record type CNAME for www
        ThirdPartyLog::log($params['logItem'], $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $params['domain'],
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'CNAME',
            'record' => $params['domain']
        ], $logCodes['send_dns_record_www_cname']);

        // Add dns record type CNAME for www
        if (!Dns::addRecord($params['domain'], 'www', [
            'record-type' => 'CNAME',
            'record' => $params['domain']
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log($params['logItem'], $project->id, $results, $logCodes['dns_record_www_cname']);

        return $result;
    }

    /**
     * @param Stores|Project|Sites $project
     * @return bool
     */
    public static function addSubDns($project)
    {
        $params = static::getProjectParams($project);

        if (!$params) {
            return false;
        }
        $logCodes = $params['logCodes'];
        $result = true;
        $subPrefix = str_replace('.', '-', $params['domain']);
        $results = [];

        // If sub domain exist, return true
        if (Dns::getRecordInfo($params['projectDomainName'], $subPrefix, [
            'type' => 'CNAME'
        ], $results)) {
            return true;
        }

        ThirdPartyLog::log($params['logItem'], $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $params['domain'],
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'CNAME',
            'record' => $params['projectDomainName']
        ], $logCodes['send_dns_record_cname']);

        // Add NS type CNAME
        if (!Dns::addRecord($params['projectDomainName'], $subPrefix, [
            'record-type' => 'CNAME',
            'record' => $params['projectDomainName']
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log($params['logItem'], $project->id, $results, $logCodes['dns_record_cname']);

        return $result;
    }

    /**
     * @param Stores|Project|Sites $project
     * @return bool
     */
    public static function removeMainDns($project)
    {
        $params = static::getProjectParams($project);

        if (!$params) {
            return false;
        }
        $logCodes = $params['logCodes'];
        $result = true;

        // Remove all dns records
        if (!Dns::removeMaster($params['domain'], $results)) {
            $result = false;
        }
        ThirdPartyLog::log($params['logItem'], $project->id, $results, $logCodes['remove_master']);

        return $result;
    }

    /**
     * @param $project
     * @return bool
     */
    public static function removeSubDns($project)
    {
        $params = static::getProjectParams($project);

        if (!$params) {
            return false;
        }
        $logCodes = $params['logCodes'];
        $result = true;
        $subPrefix = str_replace('.', '-', $params['domain']);

        if (!Dns::removeRecord($params['projectDomainName'], $subPrefix, [
            'type' => 'CNAME'
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log($params['logItem'], $project->id, $results, $logCodes['remove_record']);

        return $result;
    }


    /**
     * Add dns
     * @param Stores|Project|Sites $project
     * @return bool
     */
    public static function addDns($project)
    {
        $result = static::addMainDns($project);

        return $result;
    }

    /**
     * Remove dns
     * @param Stores|Project|Sites $project
     * @return bool
     */
    public static function removeDns($project)
    {
        $result = static::removeMainDns($project);

        return $result;
    }

    /**
     * @param string $project 'store' or 'panel'
     * @return array
     */
    public static function getLogCodes(string $project): array
    {
        return [
            'master_dns' => "$project.master_dns",
            'send_master_dns' => "$project.send_master_dns",
            'send_dns_record_a' => "$project.send_dns_record_a",
            'dns_record_a' => "$project.dns_record_a",
            'send_dns_record_www_cname' => "$project.send_dns_record_www_cname",
            'dns_record_www_cname' => "$project.dns_record_www_cname",
            'send_dns_record_cname' => "$project.send_dns_record_cname",
            'dns_record_cname' => "$project.dns_record_cname",
            'remove_master' => "$project.remove_master",
            'remove_record' => "$project.remove_record",
        ];
    }

    /**
     * @param $project
     * @return array|bool
     */
    public static function getProjectParams($project)
    {
        if ($project instanceof Stores) {
            return [
                'domain' => $project->domain,
                'projectDomainName' => Yii::$app->params['storeDomain'],
                'logCodes' => static::getLogCodes('store'),
                'logItem' => ThirdPartyLog::ITEM_BUY_STORE,
                'registrarParams' => Yii::$app->params[static::_getDns($project->domain).'.sommerce.ns'],
            ];
        } elseif ($project instanceof Project) {
            return [
                'domain' => $project->site,
                'projectDomainName' => Yii::$app->params['panelDomain'],
                'logCodes' => static::getLogCodes('panel'),
                'logItem' => ThirdPartyLog::ITEM_BUY_PANEL,
                'registrarParams' => Yii::$app->params[static::_getDns($project->domain).'.my.ns'],
            ];
        } elseif ($project instanceof Sites) {
            return [
                'domain' => $project->domain,
                'projectDomainName' => Yii::$app->params['gatewayDomain'],
                'logCodes' => static::getLogCodes('gateway'),
                'logItem' => ThirdPartyLog::ITEM_BUY_GATEWAY,
                'registrarParams' => Yii::$app->params[static::_getDns($project->domain).'.gateway.ns'],
            ];
        } else {
            return false;
        }
    }

    /**
     * Get name for params
     * @param $domain
     * @return string
     */
    protected static function _getDns($domain)
    {
        return DomainsHelper::getRegistrarName($domain);
    }
}
