<?php
namespace sommerce\components\payments;

use common\helpers\CurrencyHelper;
use common\models\store\Payments;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
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
     * @throws UnknownClassException
     * @return BasePayment
     */
    public static function getPayment($method)
    {
        if (!empty(static::$methods[$method])) {
            return static::$methods[$method];
        }

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();
        $className = (string)CurrencyHelper::getPaymentClass($method, $store->currency);

        $className = '\sommerce\components\payments\methods\\' . ucfirst($className);

        if (!class_exists($className)) {
            throw new UnknownClassException();
        }

        static::$methods[$method] = new $className(['method' => $method]);

        return static::$methods[$method];
    }
}