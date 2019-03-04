<?php

namespace sommerce\helpers;


/**
 * Class PriceHelper
 * @package sommerce\helpers
 */
class PriceHelper
{
    /**
     * @param $value
     * @param $code
     * @return mixed
     */
    public static function getPrice($value, $code) {
        $format = CurrencyHelper::getCurrencyTemplate($code);

        if (!$format) {
            return $value;
        }

        return str_replace("{{number}}", $value, $format);
    }
}