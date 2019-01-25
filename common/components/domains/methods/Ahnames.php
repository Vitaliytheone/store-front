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
     * Check domains
     * @param array $domains
     * @return array
     */
    public static function domainsCheck($domains): array
    {
        if (empty($domains)) {
            return [];
        }

        if (!is_array($domains)) {
            $domains = [$domains];
        }

        $url = Yii::$app->params['ahnames.url'];

        $options = [
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
            'domains' => implode(",", $domains)
        ];
        
        
        $result = Request::getContents($url . '/domainsCheck?' . http_build_query($options));

        return static::_processResult($result, false);
    }

    /**
     * Create contact
     * @param array $options
     * @return array
     */
    public static function contactCreate($options): array
    {
        $options = array_merge($options, [
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ]);

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/contactCreate', $options);

        return static::_processResult($result);
    }

    /**
     * Register domain
     * @param string $domain
     * @param int $contactId
     * @param int $period
     * @return array
     */
    public static function domainRegister($domain, $contactId, $period = 1): array
    {
        $options = [
            'domain' => $domain,
            'period' => $period,
            'registrant' => $contactId,
            'admin' => $contactId,
            'tech' => $contactId,
            'billing' => $contactId,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ];

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainRegister', $options);

        return static::_processResult($result);
    }

    /**
     * Get domain info
     * @param string $domain
     * @return array
     */
    public static function domainGetInfo($domain): array
    {
        $options = [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ];

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainGetInfo', $options);

        return static::_processResult($result);
    }

    /**
     * Domain Enable Who is Protect
     * @param string $domain
     * @return array
     */
    public static function domainEnableWhoisProtect($domain): array
    {
        $options = [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ];

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainEnableWhoisProtect', $options);

        return static::_processResult($result);
    }

    /**
     * Domain set ns
     * @param string $domain
     * @param array $ns
     * @return array
     */
    public static function domainSetNSs($domain, $ns = []): array
    {
        if (empty($ns)) {
            $ns = Yii::$app->params['ahnames.my.ns'];
        }

        $ns = array_filter($ns);

        $options = [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
            'nss' => implode(",", $ns)
        ];

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainSetNSs', $options);

        return static::_processResult($result);
    }

    /**
     * Domain enable lock
     * @param string $domain
     * @return array
     */
    public static function domainEnableLock($domain): array
    {
        $options = [
            'domain' => $domain,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ];

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainEnableLock', $options);

        return static::_processResult($result);
    }

    /**
     * Renew exiting domain
     *
     * Domain name
     * @param $domain string
     *
     * Current expiry date in ISO format
     * example `2012—09—25`
     * @param $expires
     *
     * Renewal period, year
     * @param $period integer
     *
     * @return array
     */
    public static function domainRenew($domain, $expires, $period = 1): array
    {
        $options = [
            'domain' => $domain,
            'expires' => $expires,
            'period' => $period,
            'auth_login' => Yii::$app->params['ahnames.login'],
            'auth_password' => Yii::$app->params['ahnames.password'],
        ];

        $url = Yii::$app->params['ahnames.url'];

        $result = CurlHelper::request($url . '/domainRenew', $options);

        return static::_processResult($result);
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