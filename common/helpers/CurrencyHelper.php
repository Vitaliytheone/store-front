<?php
namespace common\helpers;

use common\models\stores\PaymentGateways;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CurrencyHelper
 * @package common\helpers
 */
class CurrencyHelper {

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
     * Get currency symbol
     * @param string $code
     * @return mixed
     */
    public static function getCurrencySymbol($code)
    {
        $symbol = $code;

        if (!empty(Yii::$app->params['currencies'][$code])) {
            $symbol = Yii::$app->params['currencies'][$code]['symbol'];
        }

        return $symbol;
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
            $currencyOptions = Yii::$app->params['currencies'][$code];
            if (1 == (int)$currencyOptions['symbol_aligment']) {
                $template = '{{symbol}}{{value}}';
            } else if (2 == (int)$currencyOptions['symbol_aligment']) {
                $template = '{{value}}{{symbol}}';
            }
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
}