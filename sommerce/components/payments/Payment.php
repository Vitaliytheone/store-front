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
     * @param string|int $method
     * @throws UnknownClassException
     * @return BasePayment
     */
    public static function getPayment($method)
    {
        if (!empty(static::$methods[$method])) {
            return static::$methods[$method];
        }

        if (is_string($method)) {
            $paymentMethod = PaymentMethods::findOne(['method_name' => $method]);
        } else {
            $paymentMethod = PaymentMethods::findOne($method);
        }

        $className = '\sommerce\components\payments\methods\\' . ucfirst($paymentMethod->class_name);

        if (!class_exists($className)) {
            throw new UnknownClassException();
        }

        static::$methods[$paymentMethod->method_name] = new $className(['method' => $paymentMethod->method_name]);

        return static::$methods[$paymentMethod->method_name];
    }
}