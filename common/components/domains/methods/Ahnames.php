<?php

namespace common\components\domains\methods;

use common\components\domains\BaseDomain;
use common\helpers\Request;
use common\helpers\CurlHelper;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;

/**
 * Class Ahnames
 * @package common\components\domains\methods
 */
class Ahnames extends BaseDomain
{

    public const REGISTRAR_AHNAMES = 'ahnames';

    /**
     * @inheritdoc
     */
    protected static function _domainsCheckRegistrar($domains): array
    {

        $url = Yii::$app->params['ahnames.url'];

        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domains' => implode(',', $domains)
        ]);
        
        
        $result = Request::getContents($url . '/domainsCheck?' . http_build_query($options));

        return static::_processResult($result, false);
    }

    /**
     * @inheritdoc
     */
    public static function contactCreate($options): array
    {
        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($options, $defaultOptions);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/contactCreate', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainRegister($domain, $contactId, $period = 1): array
    {
        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domain' => $domain,
            'period' => $period,
            'registrant' => $contactId,
            'admin' => $contactId,
            'tech' => $contactId,
            'billing' => $contactId,
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainRegister', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainGetInfo($domain): array
    {
        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domain' => $domain,
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainGetInfo', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableWhoisProtect($domain): array
    {
        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domain' => $domain,
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainEnableWhoisProtect', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainSetNSs($domain, $ns = []): array
    {
        if (empty($ns)) {
            $ns = Yii::$app->params['ahnames.my.ns'];
        }

        $ns = array_filter($ns);

        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domain' => $domain,
            'nss' => implode(",", $ns)
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainSetNSs', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableLock($domain): array
    {
        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domain' => $domain,
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainEnableLock', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function domainRenew($domain, $expires, $period = 1): array
    {
        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domain' => $domain,
            'expires' => $expires,
            'period' => $period,
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainRenew', $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    public static function getDefaultOptions(): array
    {
        return [
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ];
    }

    /**
     * Get result
     * @param mixed $result
     * @param bool $returnError
     * @return array
     */
    protected static function _processResult($result, $returnError = true): array
    {
        if (empty($result)) {
            return [];
        }

        try {
            $result = Json::decode($result);
        } catch (InvalidArgumentException $e) {
            return [];
        }

        if (empty($result)) {
            return [];
        }

        if (!$returnError && !empty($result['_error'])) {
            return [];
        }

        return $result;
    }
}