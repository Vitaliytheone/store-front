<?php
namespace common\helpers;

/**
 * Class PriceHelper
 * @package common\helpers
 */
class PriceHelper {

    /**
     * Prepare price value
     * @param float|integer $value
     * @param string|null $currency
     */
    public static function prepare($value, $currency = null)
    {
        $min = 2;
        $delimiter = '.';
        $thousands = ' ';
        $value = (string)$value;

        if (false !== strpos($value, '.')) {
            $value = preg_replace("/0+$/", "", $value);
            $value = explode('.', $value);

            $len = strlen($value[1]);

            $min = $len > $min ? $len : $min;

            $value = $value[0] . '.' . $value[1];
        }

        $value = number_format($value, $min, $delimiter, $thousands);

        if (null !== $currency) {
            $value = str_replace('{{value}}', $value, CurrencyHelper::getCurrencyTemplate($currency));
        }

        return $value;
    }
}