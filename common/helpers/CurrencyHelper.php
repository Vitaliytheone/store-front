<?php
namespace common\helpers;

use common\models\panels\services\GetPaymentMethodsService;
use common\models\stores\PaymentGateways;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CurrencyHelper
 * @package common\helpers
 */
class CurrencyHelper {

    /**
     * @var array
     */
    protected static $_paymentMethods;

    protected static $currencyOptions = [];

    /**
     * Get currency options by code
     * @param string $code
     * @return array
     */
    public static function getCurrencyOptions(string $code):array
    {
        if (isset(static::$currencyOptions[$code])) {
            return static::$currencyOptions[$code];
        }

        static::$currencyOptions[$code] = [];

        /**
         * @var PaymentGateways $method
         */
        foreach (PaymentGateways::getMethods() as $method) {
            $availableCurrencies = (array)$method->getCurrencies();

            if (!in_array($code, $availableCurrencies)) {
                continue;
            }

            static::$currencyOptions[$code][$method->method] = [
                'url' => $method->url,
                'class_name' => $method->class_name,
                'name' => $method->name,
                'code' => $method->method,
                'position' => $method->position,
                'options' => $method->getOptions()
            ];
        }

        return static::$currencyOptions[$code];
    }

    /**
     * Get currency format template
     * @param string $code
     * @return string
     */
    public static function getCurrencyTemplate($code)
    {
        $template = '{{value}}';
        if (!empty(Yii::$app->params['currencies'][$code])) {
            $template = Yii::$app->params['currencies'][$code]['money_format'];
        }
        return $template;
    }

    /**
     * Get payments config data by code
     * @param string $code
     * @return array
     */
    public static function getPaymentsByCurrency($code)
    {
        return static::getCurrencyOptions($code);
    }

    /**
     * Get payment system class name by payment method
     * @param $paymentMethod
     * @param $code
     * @return string
     */
    public static function getPaymentClass($paymentMethod, $code = 'USD')
    {
        return ArrayHelper::getValue(static::getCurrencyOptions($code), [
            $paymentMethod,
            'class_name'
        ]);
    }

    /**
     * Get payment methods for currency
     * @param string $currency
     * @return array
     */
    public static function getPaymentMethodsByCurrency(string $currency): array
    {
        return (array)array_filter(static::getPaymentMethods(), function ($method) use ($currency) {
            return in_array($currency, $method['currency']) || in_array($currency, $method['multi_currency']);
        });
    }

    /**
     * Get all payment methods
     * @return array
     */
    public static function getPaymentMethods(): array
    {
        if (null === static::$_paymentMethods) {
            static::$_paymentMethods = Yii::$container->get(GetPaymentMethodsService::class)->get();
        }

        return (array)static::$_paymentMethods;
    }

    /**
     * @param string $currency
     * @return integer|null
     */
    public static function getCurrencyIdByCode(string $currency)
    {
        return ArrayHelper::getValue(ArrayHelper::getColumn(Yii::$app->params['legacy_currencies'], 'id'), $currency);
    }

    /**
     * @param integer $currencyId
     * @return string|null
     */
    public static function getCurrencyCodeById(int $currencyId)
    {
        foreach ((array)Yii::$app->params['legacy_currencies'] as $currency => $currencyOptions) {
            $id = ArrayHelper::getValue($currencyOptions, 'id');
            if ($id == $currencyId) {
                return $currency;
            }
        }

        return null;
    }
}