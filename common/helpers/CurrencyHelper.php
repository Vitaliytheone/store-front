<?php
namespace common\helpers;

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
}