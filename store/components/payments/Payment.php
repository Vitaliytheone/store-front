<?php

namespace store\components\payments;

use yii\base\UnknownClassException;

/**
 * Class Payment
 * @package app\components\payments
 */
class Payment {

    /**
     * @var array - methods
     */
    protected static $methods = [];

    /**
     * Get payment component by payment method name
     *
     * @param string|int $method
     * @throws UnknownClassException
     * @return BasePayment
     */
    public static function getPayment($method)
    {
        if (!empty(static::$methods[$method])) {
            return static::$methods[$method];
        }

        $className = '\store\components\payments\methods\\' . ucfirst($method);

        if (!class_exists($className)) {
            throw new UnknownClassException();
        }

        static::$methods[$method] = new $className();

        return static::$methods[$method];
    }
}