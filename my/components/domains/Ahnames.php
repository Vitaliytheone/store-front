<?php
namespace my\components\domains;

use my\helpers\CurlHelper;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Ahnames
 * @package my\components\domains
 */
class Ahnames {

    /**
     * Check domains
     * @param array $domains
     * @return array
     */
    public static function domainsCheck($domains)
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

        $result = @file_get_contents($url . '/domainsCheck?' . http_build_query($options));

        return static::_processResult($result, false);
    }

    /**
     * Create contact
     * @param array $options
     * @return array
     */
    public static function contactCreate($options)
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
    public static function domainRegister($domain, $contactId, $period = 1)
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
    public static function domainGetInfo($domain)
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
    public static function domainEnableWhoisProtect($domain)
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
    public static function domainSetNSs($domain, $ns = [])
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
    public static function domainEnableLock($domain)
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
     * Get result
     * @param mixed $result
     * @param bool $returnError
     * @return array
     */
    protected static function _processResult($result, $returnError = true)
    {
        if (empty($result)) {
            return [];
        }

        try {
            $result = Json::decode($result);
        } catch (InvalidParamException $e) {
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