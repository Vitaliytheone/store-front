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

    /**
     * @inheritdoc
     */
    protected static function _defaultAction($paramOptions, $paramLink): array
    {
        $url = Yii::$app->params['ahnames.url'];

        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, $paramOptions);

        $result = CurlHelper::request($url . $paramLink, $options);

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    protected static function _validateDomain($domain): bool
    {
        if (preg_match('/^[a-zA-Z0-9\.-]+$/iu', $domain) === 0) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected static function _domainsCheckRegistrar($domains): array
    {
        if (!static::validate(reset($domains))){
            return array_fill_keys($domains, 0);
        }

        $url = Yii::$app->params['ahnames.url'];

        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, [
            'domains' => implode(',', $domains)
        ]);
        
        
        $result = Request::getContents($url . '/domainsCheck?' . http_build_query($options));

        $resultFinal = static::_processResult($result, false);
        if (empty($resultFinal)) {
            $resultFinal = array_fill_keys($domains, 0);
        }
        return $resultFinal;
    }

    /**
     * @inheritdoc
     */
    public static function contactCreate($options): array
    {
        return static::_defaultAction($options, '/contactCreate');
    }

    /**
     * @inheritdoc
     */
    public static function domainRegister($domain, $contactId, $period = 1): array
    {
        if (!static::validate($domain)){
            return ['_error' => 'Not support IDN'];
        }

        $options = [
            'domain' => $domain,
            'period' => $period,
            'registrant' => $contactId,
            'admin' => $contactId,
            'tech' => $contactId,
            'billing' => $contactId,
        ];

        return static::_defaultAction($options, '/domainRegister');
    }

    /**
     * @inheritdoc
     */
    public static function domainGetInfo($domain): array
    {
        $options = [
            'domain' => $domain,
        ];

        return static::_defaultAction($options, '/domainGetInfo');
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableWhoisProtect($domain): array
    {
        $options = [
            'domain' => $domain,
        ];

        return static::_defaultAction($options, '/domainEnableWhoisProtect');
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

        $options = [
            'domain' => $domain,
            'nss' => implode(",", $ns)
        ];

        return static::_defaultAction($options, '/domainSetNSs');
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableLock($domain): array
    {
        $options = [
            'domain' => $domain,
        ];

        return static::_defaultAction($options, '/domainEnableLock');
    }

    /**
     * @inheritdoc
     */
    public static function domainRenew($domain, $expires, $period = 1): array
    {
        $options = [
            'domain' => $domain,
            'expires' => $expires,
            'period' => $period,
        ];

        return static::_defaultAction($options, '/domainRenew');
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