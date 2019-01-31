<?php

namespace common\components\domains;

use common\models\panels\DomainZones;
use yii\base\UnknownClassException;

/**
 * Class Domain
 * @package app\components\domains
 */
class Domain
{

    /** @var array registrars name */
    public static $registrarName;

    /** @var array of BaseDomain classes */
    protected static $_registrars = [];

    /**
     * Returns the Class created depending on the domain zone.
     *
     * @param string $domain
     * @return BaseDomain
     * @throws UnknownClassException
     */
    public static function getRegistrarClass($domain)
    {
        $name = self::getRegistrarName($domain);

        $result = self::createRegistrarClass($name);

        return $result;
    }


    /**
     * Get the name of the domain registrar
     * @param string $domain Domain name
     * @return string domain registrar name
     */
    public static function getRegistrarName($domain): string
    {

        $zone = strtoupper('.' . explode('.', $domain)[1]);
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
     * Creates an object of the required Class by name
     *
     * @param string $name Class name
     * @return BaseDomain
     * @throws UnknownClassException
     */
    public static function createRegistrarClass($name)
    {
        if (!empty(static::$_registrars[$name])) {
            return static::$_registrars[$name];
        }

        $className = __NAMESPACE__ . '\methods\\' . ucfirst($name);

        if (!class_exists($className)) {
            throw new UnknownClassException("Class {$className} does not exist");
        }

        static::$_registrars[$name] = new $className();

        return static::$_registrars[$name];
    }

}