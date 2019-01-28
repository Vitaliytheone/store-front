<?php

namespace common\components\domains\methods;

use common\components\domains\BaseDomain;
use common\helpers\Request;
use common\helpers\CurlHelper;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;


/**
 * Class Namesilo
 * @package common\components\domains\methods
 */
class Namesilo extends BaseDomain
{

    /**
     * @inheritdoc
     */
    protected static function _domainsCheckRegistrar($domains): array
    {

        $url = Yii::$app->params['namesilo.url'];

        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
            'domains' => implode(',', $domains)
        ];

        Yii::debug($options, '$options');
        $result = Request::getContents($url . '/checkRegisterAvailability?' . http_build_query($options));

        return static::_processResult($result, false);
    }

    /**
     * @inheritdoc
     */
    public static function contactCreate($options): array
    {
        $options = [
            'em' => ArrayHelper::getValue($options, 'email'),
            'fn' => ArrayHelper::getValue($options, 'first_name'),
            'ln' => ArrayHelper::getValue($options, 'last_name'),
            'cp' => ArrayHelper::getValue($options, 'organization'),
            'ad' => ArrayHelper::getValue($options, 'street1'),
            'cy' => ArrayHelper::getValue($options, 'city'),
            'st' => ArrayHelper::getValue($options, 'province'),
            'zp' => ArrayHelper::getValue($options, 'postal_code'),
            'ct' => ArrayHelper::getValue($options, 'country'),
            'ph' => ArrayHelper::getValue($options, 'voice_phone'),
            'fx' => ArrayHelper::getValue($options, 'fax_phone'),
        ];

        Yii::debug($options, '$options Contact');
        //If country is US or CA, you must use the correct abbreviation
        //Country must use the correct abbreviation

        $options = array_merge($options, [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
        ]);

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/contactAdd', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainRegister($domain, $contactId, $period = 1): array
    {
        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],

            'domain' => $domain,
            'years' => $period,
            'auto_renew' => '0',

            'contact_id' => $contactId,
        ];

        if (!empty(Yii::$app->params['namesilo.payment_id'])) {
            $options = array_merge($options, ['payment_id' => Yii::$app->params['namesilo.payment_id']]);
        }

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/registerDomain', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainGetInfo($domain): array
    {
        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
            'domain' => $domain,
        ];

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/getDomainInfo', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableWhoisProtect($domain): array
    {
        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
            'domain' => $domain,
        ];

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/addPrivacy', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainSetNSs($domain, $ns = []): array
    {
        if (empty($ns)) {
            $ns = Yii::$app->params['namesilo.my.ns'];
        }

        $ns = array_filter($ns);

        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
            'domain' => $domain,
        ];

        $options = array_merge($options, $ns);

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/changeNameServers', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableLock($domain): array
    {
        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
            'domain' => $domain,
        ];

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/domainLock', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainRenew($domain, $expires = null, $period = 1): array
    {
        $options = [
            'version' => Yii::$app->params['namesilo.version'],
            'type' => Yii::$app->params['namesilo.type'],
            'key' => Yii::$app->params['namesilo.key'],
            'domain' => $domain,
            'years' => $period,
        ];

        if (!empty(Yii::$app->params['namesilo.payment_id'])) {
            $options = array_merge($options, ['payment_id' => Yii::$app->params['namesilo.payment_id']]);
        }

        $url = Yii::$app->params['namesilo.url'];

        $result = CurlHelper::request($url . '/renewDomain', $options);

        return static::_processResult($result);
    }


    /**
     * Convert result from XML to array
     * @param mixed $result
     * @param bool $returnError
     * @return array
     */
    protected static function _processResult($result, $returnError = true): array
    {
        Yii::debug($result, 'RAW XML');
        if (empty($result)) {
            return [];
        }

        try {
            $resultRaw = json_decode(json_encode(simplexml_load_string($result)),true);
            Yii::debug($resultRaw, 'array Namesilo result');

            $resultAvailable = ArrayHelper::getValue($resultRaw, 'reply.available.domain');
            $resultAvailable = array_flip($resultAvailable);
            // fixme создать массив из ключей и значений
            Yii::debug($resultAvailable, '$resultAvailable');
            $result = array_fill_keys($resultAvailable, 1);

            $resultUnavailable = ArrayHelper::getValue($resultRaw, 'reply.unavailable.domain');
            $result += array_fill_keys($resultUnavailable, 0);

            $resultCode = ArrayHelper::getValue($resultRaw, 'reply.code');

            Yii::debug($result, 'array Namesilo result');
        } catch (InvalidArgumentException $e) {
            return [];
        }

        if (empty($result)) {
            return [];
        }

        if (!$returnError && ($resultCode != '300')) {
            return [];
        }

        return $result;
    }
}