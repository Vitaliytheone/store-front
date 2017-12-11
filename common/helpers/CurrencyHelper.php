<?php
namespace common\helpers;
use yii\helpers\ArrayHelper;

/**
 * Class CurrencyHelper
 * @package common\helpers
 */
class CurrencyHelper {

    // TODO: Update after use multiple currencies
    public static $currencies = [
        'USD' => [
            'id' => 1,
            'name' => 'United States Dollars',
            'symbol' => '$',
            'symbol_aligment' => '1',
            'gateway' => [
                'paypal' => [
                    'method_name' => 'PayPal',
                    'class_name' => "Paypal",
                    'url' => [
                        'paypalexpress',
                        'paypalstandart',
                    ],
                    'mode' => 'standart',
                    'name' => 'PayPal',
                    'minimal' => '1.00',
                    'maximal' => 0,
                    'active' => 0,
                    'fee' => 0,
                    'options' => [
                        'username' => '',
                        'password' => '',
                        'signature' => '',
                    ],
                    'type' => 0,
                    'position' => 1,
                ],
                '2checkout' => [
                    'method_name' => '2Checkout',
                    'class_name' => "Twocheckout",
                    'url' => '2checkout',
                    'mode' => 'standart',
                    'name' => '2Checkout',
                    'minimal' => '1.00',
                    'maximal' => 0,
                    'active' => 0,
                    'fee' => 0,
                    'options' => [
                        'account_number' => '',
                        'secret_word' => '',
                    ],
                    'type' => 0,
                    'position' => 2,
                ],
                'bitcoin' => [
                    'method_name' => 'Bitcoin',
                    'class_name' => "Bitcoin",
                    'url' => 'bitcoin',
                    'mode' => 'standart',
                    'name' => 'Bitcoin',
                    'minimal' => '1.00',
                    'maximal' => 0,
                    'active' => 0,
                    'fee' => 0,
                    'options' => [
                        'gateway_id' => '',
                        'gateway_secret' => '',
                    ],
                    'type' => 0,
                    'position' => 3,
                ],
            ]
        ]
    ];

    /**
     * Get currency symbol
     * @param string $code
     * @return mixed
     */
    public static function getCurrencySymbol($code)
    {
        $symbol = $code;

        if (!empty(static::$currencies[$code])) {
            $symbol = static::$currencies[$code]['symbol'];
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
        if (!empty(static::$currencies[$code])) {
            $currencyOptions = static::$currencies[$code];
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
        $currencyPayments = ArrayHelper::getValue(static::$currencies, $code, []);
        $currencyPayments = ArrayHelper::getValue($currencyPayments, 'gateway', []);

        $currencyPaymentsList = [];
        foreach ($currencyPayments as $code => $currencyPayment) {
            $currencyPaymentsList[$code] = $currencyPayment;
            $currencyPaymentsList[$code]['code'] = $code;
        }

        return $currencyPaymentsList;
    }

    /**
     * Get payment system class name by payment method
     * @param $paymentMethod
     * @param $code
     * @return mixed
     */
    public static function getPaymentClass($paymentMethod, $code = 'USD')
    {
        return ArrayHelper::getValue(static::$currencies, "$code.gateway.$paymentMethod.class_name");
    }
}