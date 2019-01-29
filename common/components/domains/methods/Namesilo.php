<?php

namespace common\components\domains\methods;

use common\components\domains\BaseDomain;
use common\helpers\Request;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


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

        $result = Request::getContents($url . '/contactAdd?' . http_build_query($options));

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
            'private' => 0,
            'auto_renew' => 0,

            'contact_id' => $contactId,
        ];

        if (!empty(Yii::$app->params['namesilo.payment_id'])) {
            $options = array_merge($options, ['payment_id' => Yii::$app->params['namesilo.payment_id']]);
        }

        $url = Yii::$app->params['namesilo.url'];

        $result = Request::getContents($url . '/registerDomain?' . http_build_query($options));

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

        $result = Request::getContents($url . '/getDomainInfo?' . http_build_query($options));

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

        $result = Request::getContents($url . '/addPrivacy?' . http_build_query($options));

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

        $result = Request::getContents($url . '/changeNameServers?' . http_build_query($options));

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

        $result = Request::getContents($url . '/domainLock?' . http_build_query($options));

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

        $result = Request::getContents($url . '/renewDomain?' . http_build_query($options));

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
        Yii::debug($result, 'RAW XML'); //todo del
//        VarDumper::dump($result."\n");

        if (empty($result)) {
            return [];
        }

        try {
            $resultRaw = json_decode(json_encode(simplexml_load_string($result)),true);
            Yii::debug($resultRaw, 'array Namesilo RAW'); //todo del

            $resultCode = (int)ArrayHelper::getValue($resultRaw, 'reply.code');

            if ($returnError && (!in_array($resultCode, [300, 250, 251, 252, 253, 255, 256, 301, 302, 201]))) {
                Yii::error($resultCode, '$resultCode');
                return ['_error' => ArrayHelper::getValue($resultRaw, 'reply.detail')];
            }

            $resultType = (string)ArrayHelper::getValue($resultRaw, 'request.operation');

            switch ($resultType) {
                case 'checkRegisterAvailability':
                    $resultAvailable = $resultUnavailable = [];
                    $resultAvailableRaw = (array)ArrayHelper::getValue($resultRaw, 'reply.available.domain');
                    if (!empty($resultAvailableRaw)) {
                        foreach ($resultAvailableRaw as $item) {
                            $resultAvailable[$item] = 1;
                        }
                        Yii::debug($resultAvailable, '$resultAvailable'); //todo del
                    }


                    $resultUnavailableRaw = (array)ArrayHelper::getValue($resultRaw, 'reply.unavailable.domain');
                    if (!empty($resultUnavailableRaw)) {
                        foreach ($resultUnavailableRaw as $item) {
                            $resultUnavailable[$item] = 0;
                        }
                        Yii::debug($resultUnavailable, '$resultUnavailable');
                    }

                    $resultFinal = ArrayHelper::merge($resultAvailable, $resultUnavailable);

                    Yii::debug($resultFinal, 'array Namesilo result'); //todo del
                    break;

                case 'contactAdd':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.contact_id');
                    break;

                case 'registerDomain':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.detail');
                    $resultFinal['domain'] = ArrayHelper::getValue($resultRaw, 'reply.domain');
                    $resultFinal['order_amount'] = ArrayHelper::getValue($resultRaw, 'reply.order_amount');
                    $resultFinal['password'] = '';
                    break;

                case 'getDomainInfo':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.detail');
                    $resultFinal['created'] = ArrayHelper::getValue($resultRaw, 'reply.created');
                    $resultFinal['expires'] = ArrayHelper::getValue($resultRaw, 'reply.expires');
                    $resultFinal['status'] = ArrayHelper::getValue($resultRaw, 'reply.status');
                    $resultFinal['locked'] = ArrayHelper::getValue($resultRaw, 'reply.locked');
                    $resultFinal['private'] = ArrayHelper::getValue($resultRaw, 'reply.private');
                    $resultFinal['auto_renew'] = ArrayHelper::getValue($resultRaw, 'reply.auto_renew');
                    $resultFinal['traffic_type'] = ArrayHelper::getValue($resultRaw, 'reply.traffic_type');
                    $resultFinal['email_verification_required'] = ArrayHelper::getValue($resultRaw, 'reply.email_verification_required');
                    $resultFinal['nameservers'] = ArrayHelper::getValue($resultRaw, 'reply.nameservers');
                    $resultFinal['contact_ids'] = ArrayHelper::getValue($resultRaw, 'reply.contact_ids');
                    $resultFinal['registrar'] = 'namesilo';
                    break;

                case 'addPrivacy':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.detail');
                    break;

                case 'changeNameServers':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.detail');
                    break;

                case 'domainLock':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.detail');
                    break;

                case 'renewDomain':
                    $resultFinal['id'] = ArrayHelper::getValue($resultRaw, 'reply.detail');
                    $resultFinal['domain'] = ArrayHelper::getValue($resultRaw, 'reply.domain');
                    $resultFinal['order_amount'] = ArrayHelper::getValue($resultRaw, 'reply.order_amount');
                    break;

            }


        } catch (InvalidArgumentException $e) {
            return ['_error' => $e->getMessage()];
        }

        if (empty($resultFinal)) {
            return [];
        }

//        VarDumper::dump($resultFinal); //todo del
        return $resultFinal;
    }
}