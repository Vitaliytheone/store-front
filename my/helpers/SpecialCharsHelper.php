<?php

namespace my\helpers;


/**
 * Class SpecialCharsHelper
 * @package my\helpers
 */
class SpecialCharsHelper
{

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function multiPurifier($value) {
        if (empty($value)) {
            return $value;
        }

        if (is_string($value)) {
            return htmlspecialchars($value);
        }

        $elem = isset($value[0]) ? $value[0] : $value;

        if (is_array($value)) {
            if (is_object($elem)) {
                foreach ($value as $item) {
                    array_walk_recursive($item, function (&$val) {
                        if (is_string($val)) {
                            $val = htmlspecialchars($val);
                        }
                    });
                }
            }
            array_walk_recursive($value, function(&$val) {
                if (is_string($val)) {
                    $val = htmlspecialchars($val);
                }
            });
        }

        return $value;
    }

    /**
     * @param $value
     * @return array|string
     */
    public static function multiPurifierDecode($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (is_string($value)) {
            return htmlspecialchars_decode($value);
        }

        $elem = isset($value[0]) ? $value[0] : $value;

        if (is_array($value)) {
            if (is_object($elem)) {
                foreach ($value as $item) {
                    array_walk_recursive($item, function (&$val) {
                        if (is_string($val)) {
                            $val = htmlspecialchars_decode($val);
                        }
                    });
                }
            }
            array_walk_recursive($value, function(&$val) {
                if (is_string($val)) {
                    $val = htmlspecialchars_decode($val);
                }
            });
        }

        return $value;
    }
}