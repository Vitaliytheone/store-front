<?php

namespace common\components\domains;

use yii\base\Component;

/**
 * Class BaseDomain
 * @package common\components\domains
 */
abstract class BaseDomain extends Component
{

    /**
     * Checks the domain on the possibility of registration
     * @param array $domains
     * @return array API response
     */
    public static function domainsCheck($domains): array
    {
        if (empty($domains)) {
            return [];
        }

        if (!is_array($domains)) {
            $domains = [$domains];
        }

        return static::_domainsCheckRegistrar($domains);
    }

    /**
     * Validate domain for registration
     * @param string $domain
     * @return bool
     */
    public static function validate($domain): bool
    {
        if (mb_stripos($domain, '.') !== false) {
            $domain = explode('.', $domain)[0];
        }

        if (mb_stripos($domain, 'xn--') !== false) {
            return false;
        }

        return static::_validateDomain($domain);
    }

    /**
     * Validate domain for registration with registrar condition
     * @param string $domain
     * @return bool
     */
    abstract protected static function _validateDomain($domain): bool;

    /**
     * Get result from request
     * @param array $paramOptions
     * @param string $paramLink
     * @return array
     */
    abstract protected static function _defaultAction($paramOptions, $paramLink): array;

    /**
     * Checks the domain on the possibility of registration
     * @param array $domains
     * @return array API response
     */
    abstract protected static function _domainsCheckRegistrar($domains): array;

    /**
     * Creates a user contact for registration and WHOIS domain
     * @param array $options
     * @return array API response
     */
    abstract public static function contactCreate($options): array;

    /**
     * Register a domain
     * @param string $domain
     * @param int $contactId
     * @param int $period
     * @return array API response
     */
    abstract public static function domainRegister($domain, $contactId, $period = 1): array;

    /**
     * Get essential information on a domain
     * @param string $domain
     * @return array API response
     */
    abstract public static function domainGetInfo($domain): array;


    /**
     * Add WHOIS protect (Privacy) to a domain.
     * @param string $domain
     * @return array API response
     */
    abstract public static function domainEnableWhoisProtect($domain): array;

    /**
     * Change the NameServers for domain
     * @param string $domain
     * @param array $ns nameservers
     * @return array API response
     */
    abstract public static function domainSetNSs($domain, $ns = []): array;

    /**
     * Set the specified domain to be locked.
     * @param string $domain
     * @return array API response
     */
    abstract public static function domainEnableLock($domain): array;

    /**
     * Renew a domain in your account for the specified number of years.
     * @param string $domain Domain name
     * @param int $expires Current expiry date in ISO format example `2019—09—25`
     * @param int $period Renewal period, year
     * @return array API response
     */
    abstract public static function domainRenew($domain, $expires, $period = 1): array;


    /**
     * Converts a response from an API into an array.
     * @param mixed $result
     * @param bool $returnError
     * @return mixed
     */
    abstract protected static function _processResult($result, $returnError = true);

    /**
     * Get authorization data from parameters for a Class as an array
     * @return array
     */
    abstract public static function getDefaultOptions(): array;

}