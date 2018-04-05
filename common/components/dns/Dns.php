<?php
namespace common\components\dns;

use Yii;
use my\helpers\CurlHelper;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Dns
 * @package common\components\dns
 */
class Dns {

    /**
     * Get domain info
     * @param string $domain
     * @param mixed $result
     * @return bool
     */
    public static function getZoneInfo($domain, &$result)
    {
        $options = [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain
        ];

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/get-zone-info.json?' . http_build_query($options));

        if (!$result) {
            return false;
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {
            return false;
        }

        if (1 == ArrayHelper::getValue($result, 'status')) {
            return true;
        }

        return false;
    }

    /**
     * Add master
     * @param string $domain
     * @param array $nss
     * @param mixed $result
     * @return bool
     */
    public static function addMaster($domain, $nss, &$result)
    {
        $options = [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'zone-type' => 'master',
            'ns' => array_values($nss),
        ];

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/register.json?' . http_build_query($options));

        if (!$result) {
            return false;
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {

        }

        $status = ArrayHelper::getValue($result, 'status');

        if ($status && 'success' == strtolower($status)) {
            return true;
        }

        return false;
    }

    /**
     * Remove master
     * @param string $domain
     * @param mixed $result
     * @return bool
     */
    public static function removeMaster($domain, &$result)
    {
        $result = [];

        $options = [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
        ];

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/delete.json?' . http_build_query($options));

        if (!$result) {
            return false;
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {

        }

        $status = ArrayHelper::getValue($result, 'status');

        if ($status && 'success' == strtolower($status)) {
            return true;
        }

        return false;
    }

    /**
     * Add record
     * @param string $domain
     * @param string $host
     * @param array $options
     * @param mixed $result
     * @return bool
     */
    public static function addRecord($domain, $host = '', $options = [], &$result)
    {
        $result = [];

        $options = array_merge([
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'record-type' => 'A',
            'host' => $host,
            'ttl' => 1800,
            'record' => Yii::$app->params['dnsIp']
        ], $options);

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/add-record.json?' . http_build_query($options));

        if (!$result) {
            return false;
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {

        }

        $status = ArrayHelper::getValue($result, 'status');

        if ($status && 'success' == strtolower($status)) {
            return true;
        }

        return false;
    }

    /**
     * List records dns
     * @param string $domain
     * @param string $host
     * @param array $options
     * @param mixed $result
     * @return array
     */
    public static function listRecords($domain, $host = '', $options = [], &$result)
    {
        $result = [];

        $options = array_merge([
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'type' => 'A',
            'host' => $host
        ], $options);

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/records.json?' . http_build_query($options));

        if (!$result) {
            return [];
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {

        }

        if (empty($result)) {
            return [];
        }

        $status = ArrayHelper::getValue($result, 'status');

        if ($status) {
            return [];
        }

        return $result;
    }

    /**
     * Get record info
     * @param string $domain
     * @param string $host
     * @param array $options
     * @param array $result
     */
    public static function getRecordInfo($domain, $host = '', $options = [], &$result)
    {
        $records = static::listRecords($domain, $host, $options, $listResult);

        foreach ($records as $record) {
            $id = ArrayHelper::getValue($record, 'id');

            if (!$id) {
                continue;
            }

            $result = $record;

            return true;
        }

        return false;
    }

    /**
     * Remove record
     * @param string $domain
     * @param string $host
     * @param array $options
     * @param array $results
     */
    public static function removeRecord($domain, $host = '', $options = [], &$results)
    {
        $records = static::listRecords($domain, $host, $options, $listResult);

        foreach ($records as $record) {
            $id = ArrayHelper::getValue($record, 'id');

            if (!$id) {
                continue;
            }

            static::_removeRecord($id, $domain, $result);

            $results[] = $result;
        }

        return true;
    }

    /**
     * Remove single record by id
     * @param $id
     * @param $domain
     * @param $result
     * @return bool
     */
    private static function _removeRecord($id, $domain, &$result)
    {
        $options = [
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'domain-name' => $domain,
            'record-id' => $id
        ];

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/delete-record.json?' . http_build_query($options));

        if (!$result) {
            return false;
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {

        }

        $status = ArrayHelper::getValue($result, 'status');

        if ($status && 'success' == strtolower($status)) {
            return true;
        }
    }

    /**
     * Add dns
     * @param array $options
     * @param mixed $results
     * @return array
     */
    public static function listZones($options = [], &$results)
    {
        $result = [];

        $page = 1;
        $options['rows-per-page'] = ArrayHelper::getValue($options, 'rows-per-page', 100);
        do {
            $pageResult = static::_listZones($page, $options, $results);
            $page++;

            $result = array_merge($result, $pageResult);

            if ($options['rows-per-page'] > count($pageResult)) {
                break;
            }
        } while (!empty($pageResult));


        return $result;
    }

    /**
     * Add dns
     * @param int $page
     * @param array $options
     * @param mixed $result
     * @return array
     */
    protected static function _listZones($page = 1, $options = [], &$result)
    {
        $result = [];

        $options = array_merge([
            'auth-id' => Yii::$app->params['dnsId'],
            'auth-password' => Yii::$app->params['dnsPassword'],
            'rows-per-page' => 100,
            'page' => $page
        ], $options);

        $host = Yii::$app->params['dnsService'];
        $result = CurlHelper::request($host . '/dns/list-zones.json?' . http_build_query($options));

        if (!$result) {
            return [];
        }

        try {
            $result = @Json::decode($result);
        } catch (InvalidParamException $e) {

        }

        if (empty($result)) {
            return [];
        }

        $status = ArrayHelper::getValue($result, 'status');

        if ($status) {
            return [];
        }

        return $result;
    }
}