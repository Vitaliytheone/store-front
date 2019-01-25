<?php

namespace common\components\domains;

use yii\base\Component;
use yii\base\UnknownClassException;
use common\models\panels\DomainZones;

/**
 * Class BaseDomain
 * @package common\components\domains
 */
abstract class BaseDomain extends Component
{
    public const REGISTRAR_AHNAMES = 'ahnames';
    public const REGISTRAR_NAMESILO = 'namesilo';


    /**
     * Creates an object of the required class by domain
     *
     * @param string $domain
     * @throws UnknownClassException
     * @return BaseDomain
     */
    public static function getRegistrarClass($domain)
    {
        $method = self::getRegistrarName($domain);

        // todo возможно надо добавить статическую переменную для хранения
        $className =  __NAMESPACE__ . '\methods\\' . ucfirst($method);

        if (!class_exists($className)) {
            throw new UnknownClassException("Class {$className} does not exist");
        }
        $className = new $className;

        return $className;
    }

    /**
     * Checks the domain on the possibility of registration
     * @param array $domains
     * @return array API response
     */
    abstract public static function domainsCheck($domains): array;


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
     * @return array result as an array
     */
    abstract protected static function _processResult($result, $returnError = true): array;

    /**
     * Get the name of the domain registrar
     * @param string $domain Domain name
     * @return string domain registrar name
     */
    public static function getRegistrarName($domain): string
    {
        $zone = strtoupper('.'.explode('.', $domain)[1]);

        $registrar = DomainZones::find()->where(['zone' => $zone])->one()->registrar;
        \Yii::debug($registrar, '$registrar'); // todo del
        return $registrar;
    }
}