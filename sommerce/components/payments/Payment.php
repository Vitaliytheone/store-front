<?php

namespace sommerce\components\payments;

use common\models\stores\PaymentMethods;
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
     * @param PaymentMethods $method
     * @throws UnknownClassException
     * @return BasePayment
     */
    public static function getPayment($method)
    {
        if (!empty(static::$methods[$method->method_name])) {
            return static::$methods[$method->method_name];
        }

        $className = '\sommerce\components\payments\methods\\' . ucfirst($method->class_name);

        if (!class_exists($className)) {
            throw new UnknownClassException();
        }

        static::$methods[$method->method_name] = new $className(['method' => $method->method_name]);

        return static::$methods[$method->method_name];
    }
}