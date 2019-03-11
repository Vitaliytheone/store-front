<?php
namespace control_panel\helpers;

/**
 * Class PriceHelper
 * @package control_panel\helpers
 */
class PriceHelper {

    /**
     * Prepare price value
     * @param float|integer $value
     * @param int $min
     */
    public static function prepare($value, $min = 2, $delimiter = '.')
    {
        $value = (string)$value;
        if (false === strpos($value, $delimiter)) {
            return number_format($value, $min, $delimiter, ' ');
        }

        $value = preg_replace("/0+$/", "", $value);
        $value = explode($delimiter, $value);

        $len = strlen($value[1]);

        return number_format($value[0] . $delimiter . $value[1], $len > $min ? $len : $min, $delimiter     , ' ');
    }
}