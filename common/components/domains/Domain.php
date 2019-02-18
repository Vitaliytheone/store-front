<?php

namespace common\components\domains;

use yii\base\UnknownClassException;

/**
 * Class Domain
 * @package app\components\domains
 */
class Domain
{

    /** @var array of BaseDomain classes */
    protected static $_registrars = [];


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