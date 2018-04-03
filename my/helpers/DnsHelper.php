<?php
namespace my\helpers;

use Yii;
use common\components\dns\Dns;
use common\models\panels\ThirdPartyLog;
use common\models\panels\Project;

/**
 * Class DnsHelper
 * @package my\helpers
 */
class DnsHelper {

    /**
     * Add main domain dns
     * @param Project $project
     */
    public static function addMainDns(Project $project)
    {
        $result = true;
        $domain = $project->site;

        $results = [];

        // If domain exist, return true
        if (Dns::getZoneInfo($domain, $results)) {
            return true;
        }

        // Add master
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'zone-type' => 'master',
            'ns' => [
                'ns1.perfectdns.com',
                'ns2.perfectdns.com',
                'ns3.perfectdns.com'
            ],
        ], 'panel.send_master_dns');
        // Add master
        if (!Dns::addMaster($domain, $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $results, 'panel.master_dns');


        // Add dns record type A
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'A',
            'record' => Yii::$app->params['dnsIp']
        ], 'panel.send_dns_record_a');

        // Add dns record type A
        if (!Dns::addRecord($domain, '', [
            'record-type' => 'A',
            'record' => Yii::$app->params['dnsIp']
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $results, 'panel.dns_record_a');


        // Add dns record type CNAME for www
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'CNAME',
            'record' => $domain
        ], 'panel.send_dns_record_www_cname');

        // Add dns record type CNAME for www
        if (!Dns::addRecord($domain, 'www', [
            'record-type' => 'CNAME',
            'record' => $domain
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $results, 'panel.dns_record_www_cname');

        return $result;
    }

    /**
     * Add subdomain dns
     * @param Project $project
     */
    public static function addSubDns(Project $project)
    {
        $result = true;
        $domain = $project->site;
        $subPrefix = str_replace('.', '-', $domain);
        $panelDomainName = Yii::$app->params['panelDomain'];

        $results = [];

        // If sub domain exist, return true
        if (Dns::getRecordInfo($panelDomainName, $subPrefix, [
            'type' => 'CNAME'
        ], $results)) {
            return true;
        }

        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'host' => '',
            'ttl' => 1800,
            'record-type' => 'CNAME',
            'record' => $panelDomainName
        ], 'panel.send_dns_record_cname');

        // Add NS type CNAME
        if (!Dns::addRecord($panelDomainName, $subPrefix, [
            'record-type' => 'CNAME',
            'record' => $panelDomainName
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $results, 'panel.dns_record_cname');

        return $result;
    }

    /**
     * Remove main domain dns
     * @param Project $project
     */
    public static function removeMainDns(Project $project)
    {
        $result = true;
        $domain = $project->site;

        // Remove all dns records
        if (!Dns::removeMaster($domain, $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_PANEL, $project->id, $results, 'panel.remove_master');

        return $result;
    }

    /**
     * Remove subdomain dns
     * @param Project $project
     */
    public static function removeSubDns(Project $project)
    {
        $result = true;
        $domain = $project->site;
        $subPrefix = str_replace('.', '-', $domain);
        $panelDomainName = Yii::$app->params['panelDomain'];

        if (!Dns::removeRecord($panelDomainName, $subPrefix, [
            'type' => 'CNAME'
        ], $results)) {
            $result = false;
        }
        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_PANEL, $project->id, $results, 'panel.remove_record');

        return $result;
    }


    /**
     * Add dns
     * @param Project $project
     */
    public static function addDns(Project $project)
    {
        $result = static::addMainDns($project);

        $result = static::addSubDns($project) && $result;

        return $result;
    }

    /**
     * Remove dns
     * @param Project $project
     */
    public static function removeDns(Project $project)
    {
        $result = static::removeMainDns($project);

        $result = static::removeSubDns($project) && $result;

        return $result;
    }
}