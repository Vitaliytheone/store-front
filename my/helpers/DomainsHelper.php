<?php

namespace my\helpers;

use Yii;
use common\components\domains\Domain;
use common\models\panels\Domains;
use common\models\panels\DomainZones;
use common\models\panels\Orders;
use common\models\panels\Params;

/**
 * Class DomainsHelper
 * @package my\helpers
 */
class DomainsHelper
{

    /** @var array registrars name */
    public static $registrarName;

    /**
     * Prepare domain to idn format
     * @param string $domain
     * @return string
     */
    public static function idnToAscii($domain): string
    {
        $domain = trim($domain);

        if (!extension_loaded('intl')) {
            return $domain;
        }

        if (!preg_match('/^[a-z0-9-\.]+$/i', $domain)) {
            $domain = idn_to_ascii($domain, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
        }
        return $domain;
    }

    /**
     * Prepare domain from idn format
     * @param string $domain
     * @return string
     */
    public static function idnToUtf8($domain): string
    {
        $domain = trim((string)$domain);

        if (!extension_loaded('intl')) {
            return $domain;
        }

        if (false !== mb_stripos($domain, 'xn--')) {
            if (false === mb_stripos($domain, ' ')) {
                return idn_to_utf8($domain, IDNA_NONTRANSITIONAL_TO_UNICODE, INTL_IDNA_VARIANT_UTS46);
            }

            if (preg_match('/(xn\-\-[a-z0-9\.-]+)/is', $domain, $domainMatch)) {
                $domain = str_replace($domainMatch[1], idn_to_utf8($domainMatch[1], IDNA_NONTRANSITIONAL_TO_UNICODE, INTL_IDNA_VARIANT_UTS46), $domain);
            }
        }

        return $domain;
    }

    /**
     * Get the names of all registrars
     * @return array
     */
    public static function getAllRegistrars(): array
    {
        $result = DomainZones::find()->select('registrar')->distinct()->asArray()->column();
        return $result;
    }

    /**
     * Returns the Class created depending on the domain zone.
     *
     * @param string $domain
     * @return \common\components\domains\BaseDomain
     * @throws \yii\base\UnknownClassException
     */
    public static function getRegistrarClass($domain)
    {
        $name = static::getRegistrarName($domain);

        $result = Domain::createRegistrarClass($name);

        return $result;
    }

    /**
     * Get the name of the domain registrar
     * @param string $domain Domain name
     * @return string domain registrar name
     */
    public static function getRegistrarName($domain): string
    {

        $domain = static::idnToUtf8($domain);

        $zone = mb_strtoupper('.' . explode('.', $domain)[1]);
        if (empty($zone)) {
            return '';
        }


        if (empty(static::$registrarName[$zone]['registrar'])) {
            static::$registrarName = DomainZones::find()->asArray()->indexBy('zone')->all();
        }

        if (empty(static::$registrarName[$zone]['registrar'])) {
            return '';
        }

        return static::$registrarName[$zone]['registrar'];
    }

    /**
     * Is domain available
     * @param string $domain
     * @return bool
     * @throws \yii\base\UnknownClassException
     */
    public static function isDomainAvailable($domain): bool
    {
        if (empty($domain)) {
            return false;
        }

        $domain = mb_strtolower(trim($domain));

        $registrar = static::getRegistrarClass($domain);
        $result = $registrar::domainsCheck($domain);

        if (empty($result[$domain])) {
            return false;
        }

        $existsDomain = Orders::find()->andWhere([
            'domain' => static::idnToAscii($domain),
            'item' => Orders::ITEM_BUY_DOMAIN,
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID,
                Orders::STATUS_ADDED,
                Orders::STATUS_ERROR
            ]
        ])->exists();

        if ($existsDomain) {
            return false;
        }

        return true;
    }

    /**
     * Check if contact_id exist
     * @param string $registrar name of registrar for check
     * @param bool $contact if True return contact_id
     * @return bool|string
     */
    public static function checkContactExist($registrar, $contact = false)
    {
        if ($registrar == Domains::REGISTRAR_NAMESILO) {
            $namesiloParams = Params::get(Params::CATEGORY_SERVICE, Params::CODE_NAMESILO);
            $cid = $namesiloParams['namesilo.contact_id'] ?? '';
            if (!empty($cid)) {
                return $contact ? $cid : true;
            }
        }

        if ($registrar == Domains::REGISTRAR_AHNAMES) {
            $ahnamesParams = Params::get(Params::CATEGORY_SERVICE, Params::CODE_AHNAMES);
            $cid = $ahnamesParams['ahnames.contact_id'] ?? '';
            if (!empty($cid)) {
                return $contact ? $cid : true;
            }
        }

        return false;
    }


    /**
     * Get domain zones
     * @return array
     */
    public static function getDomainZones(): array
    {
        $zones = [];

        foreach (DomainZones::find()->all() as $zone) {
            $zones[$zone->id] = $zone->zone . ' — $' . $zone->price_register;
        }

        return $zones;
    }
}