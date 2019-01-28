<?php

namespace my\helpers;

use common\models\panels\DomainZones;

/**
 * Class DomainsHelper
 * @package my\helpers
 */
class DomainsHelper {

    /**
     * Prepare domain to idn format
     * @param string $domain
     * @return string
     */
    public static function idnToAscii($domain)
    {
        $domain = trim($domain);

        if (!extension_loaded('intl')) {
            return $domain;
        }

        if (!preg_match('/^[a-z0-9-\.]+$/i', $domain)) {
            $domain = idn_to_ascii($domain);
        }
        return $domain;
    }

    /**
     * Prepare domain from idn format
     * @param string $domain
     * @return string
     */
    public static function idnToUtf8($domain)
    {
        $domain = trim((string)$domain);

        if (!extension_loaded('intl')) {
            return $domain;
        }
        
        if (false !== stripos($domain, 'xn--')) {
            if (false === stripos($domain, ' ')) {
                return idn_to_utf8($domain);
            }

            if (preg_match('/(xn\-\-[a-z0-9\.-]+)/is', $domain, $domainMatch)) {
                $domain = str_replace($domainMatch[1], idn_to_utf8($domainMatch[1]), $domain);
            }
        }

        return $domain;
    }

    /**
     * Get the names of all registrars with a capital letter
     * @return array
     */
    public static function getAllRegistrars(): array
    {
        $result = DomainZones::find()->select('registrar')->distinct()->asArray()->all();
        $result = array_column($result, 'registrar');
        array_walk($result, function(&$value) {
            $value = ucfirst($value);
        });
        return $result;
    }
}