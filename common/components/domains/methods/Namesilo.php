<?php

namespace common\components\domains\methods;

use common\components\domains\BaseDomain;
use common\helpers\Request;
use common\models\panels\Domains;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use common\models\panels\Params;

/**
 * Class Namesilo
 * @package common\components\domains\methods
 */
class Namesilo extends BaseDomain
{

    public const URL_PROD = 'https://www.namesilo.com/api';
    public const URL_DEV = 'http://sandbox.namesilo.com/api';

    protected static $_paramsNamesilo;

    public function init()
    {
        parent::init();

        if (empty(static::$_paramsNamesilo)) {
            static::$_paramsNamesilo = Params::get(Params::CATEGORY_SERVICE, Params::CODE_NAMESILO);
        }
    }

    /**
     * Return url depended from environment (dev/prod)
     * @return string
     */
    private static function _setUrl(): string
    {
        if (!empty(static::$_paramsNamesilo['namesilo.testmode'])) {
            return self::URL_DEV;
        }
        return self::URL_PROD;
    }

    /**
     * @inheritdoc
     */
    protected static function _defaultAction($paramOptions, $paramLink): array
    {
        $url = static::_setUrl();

        $defaultOptions = static::getDefaultOptions();
        $options = array_merge($defaultOptions, $paramOptions);

        $result = Request::getContents($url . $paramLink . http_build_query($options));

        return static::_processResult($result);
    }

    /**
     * @inheritdoc
     */
    protected static function _validateDomain($domain): bool
    {
        if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]$/iu', $domain) === 0) {
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

        $options = [
            'domains' => implode(',', $domains)
        ];

        $resultRaw = static::_defaultAction($options, '/checkRegisterAvailability?');
        if (!empty($resultRaw['_error'])) {
            return array_fill_keys($domains, 0);
        }

        $resultAvailable = $resultUnavailable = [];

        $resultAvailableRaw = (array)ArrayHelper::getValue($resultRaw, 'reply.available.domain');
        if (!empty($resultAvailableRaw)) {
            foreach ($resultAvailableRaw as $item) {
                $resultAvailable[$item] = 1;
            }
        }

        $resultUnavailableRaw = (array)ArrayHelper::getValue($resultRaw, 'reply.unavailable.domain');
        if (!empty($resultUnavailableRaw)) {
            foreach ($resultUnavailableRaw as $item) {
                $resultUnavailable[$item] = 0;
            }
        }

        $resultFinal = ArrayHelper::merge($resultAvailable, $resultUnavailable);
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
        if (!empty(static::$_paramsNamesilo['namesilo.contact_id'])) {
            return ['id' => static::$_paramsNamesilo['namesilo.contact_id']];
        }

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

        $resultRaw = static::_defaultAction($options, '/contactAdd?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = ['id' => ArrayHelper::getValue($resultRaw, 'reply.contact_id')];

        return $resultFinal;
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
            'years' => $period,
            'private' => 0,
            'auto_renew' => 0,
            'contact_id' => $contactId,
        ];

        if (!empty(static::$_paramsNamesilo['namesilo.payment_id'])) {
            $options = array_merge($options, ['payment_id' => static::$_paramsNamesilo['namesilo.payment_id']]);
        }

        $resultRaw = static::_defaultAction($options, '/registerDomain?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = [
            'id' => ArrayHelper::getValue($resultRaw, 'reply.detail'),
            'domain' => ArrayHelper::getValue($resultRaw, 'reply.domain'),
            'order_amount' => ArrayHelper::getValue($resultRaw, 'reply.order_amount'),
            'password' => '',
        ];

        return $resultFinal;
    }

    /**
     * @inheritdoc
     */
    public static function domainGetInfo($domain): array
    {
        $options = [
            'domain' => $domain,
        ];

        $resultRaw = static::_defaultAction($options, '/getDomainInfo?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = [
            'id' => ArrayHelper::getValue($resultRaw, 'reply.detail'),
            'created' => ArrayHelper::getValue($resultRaw, 'reply.created'),
            'expires' => ArrayHelper::getValue($resultRaw, 'reply.expires'),
            'status' => ArrayHelper::getValue($resultRaw, 'reply.status'),
            'locked' => ArrayHelper::getValue($resultRaw, 'reply.locked'),
            'private' => ArrayHelper::getValue($resultRaw, 'reply.private'),
            'auto_renew' => ArrayHelper::getValue($resultRaw, 'reply.auto_renew'),
            'traffic_type' => ArrayHelper::getValue($resultRaw, 'reply.traffic_type'),
            'email_verification_required' => ArrayHelper::getValue($resultRaw, 'reply.email_verification_required'),
            'nameservers' => ArrayHelper::getValue($resultRaw, 'reply.nameservers'),
            'contact_ids' => ArrayHelper::getValue($resultRaw, 'reply.contact_ids'),
            'registrar' => Domains::REGISTRAR_NAMESILO,
        ];

        return $resultFinal;
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableWhoisProtect($domain): array
    {
        $options = [
            'domain' => $domain,
        ];

        $resultRaw = static::_defaultAction($options, '/addPrivacy?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = ['id' => ArrayHelper::getValue($resultRaw, 'reply.detail')];

        return $resultFinal;
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
            'domain' => $domain,
        ];

        $options = array_merge($options, $ns);

        $resultRaw = static::_defaultAction($options, '/changeNameServers?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = ['id' => ArrayHelper::getValue($resultRaw, 'reply.detail')];

        return $resultFinal;
    }

    /**
     * @inheritdoc
     */
    public static function domainEnableLock($domain): array
    {
        $options = [
            'domain' => $domain,
        ];

        $resultRaw = static::_defaultAction($options, '/domainLock?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = ['id' => ArrayHelper::getValue($resultRaw, 'reply.detail')];

        return $resultFinal;
    }

    /**
     * @inheritdoc
     */
    public static function domainRenew($domain, $expires = null, $period = 1): array
    {
        $options = [
            'domain' => $domain,
            'years' => $period,
        ];

        if (!empty(static::$_paramsNamesilo['namesilo.payment_id'])) {
            $options = array_merge($options, ['payment_id' => static::$_paramsNamesilo['namesilo.payment_id']]);
        }

        $resultRaw = static::_defaultAction($options, '/renewDomain?');
        if (!empty($resultRaw['_error'])) {
            return $resultRaw;
        }

        $resultFinal = [
            'id' => ArrayHelper::getValue($resultRaw, 'reply.detail'),
            'domain' => ArrayHelper::getValue($resultRaw, 'reply.domain'),
            'order_amount' => ArrayHelper::getValue($resultRaw, 'reply.order_amount'),
        ];

        return $resultFinal;
    }


    /**
     * @inheritdoc
     */
    public static function getDefaultOptions(): array
    {
        return [
            'version' => static::$_paramsNamesilo['namesilo.version'],
            'type' => static::$_paramsNamesilo['namesilo.type'],
            'key' => static::$_paramsNamesilo['namesilo.key'],
        ];
    }

    /**
     * Convert result from XML to array
     * @param mixed $result
     * @param bool $returnError
     * @return mixed
     */
    protected static function _processResult($result, $returnError = true)
    {

        if (empty($result)) {
            return ['_error' => 'empty response'];
        }

        try {
            $resultRaw = json_decode(json_encode(simplexml_load_string($result)), true);

            $resultCode = (int)ArrayHelper::getValue($resultRaw, 'reply.code');

            if ($returnError && (!in_array($resultCode, [300, 250, 251, 252, 253, 255, 256, 301, 302, 201]))) {
                return ['_error' => ArrayHelper::getValue($resultRaw, 'reply.detail'), 'code' => $resultCode];
            }

        } catch (InvalidArgumentException $e) {
            return ['_error' => $e->getMessage()];
        }

        return $resultRaw;
    }
}