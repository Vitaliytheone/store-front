<?php
namespace frontend\components\payments;

use Yii;
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
     * @param string $method
     * @return BasePayment
     */
    public static function getPayment($method)
    {
        if (!empty(static::$methods[$method])) {
            return static::$methods[$method];
        }

        $cleanedMethod = preg_replace('/^[^A-Za-z]+/', '', $method);

        $className = '\frontend\components\payments\methods\\' . (ucfirst($cleanedMethod));

        if (!class_exists($className)) {
            throw new UnknownClassException();
        }

        static::$methods[$method] = new $className();

        return static::$methods[$method];
    }
}