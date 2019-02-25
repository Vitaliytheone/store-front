<?php

namespace control_panel\helpers;

class StringHelper {

    /**
     * Generate random
     * @param int $length
     * @param string $alphabet
     * @return string
     */
    public static function randomString($length = 8, $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789") {
        $pass = '';
        $stringLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $stringLength);
            $pass .= $alphabet[$n];
        }
        return $pass;
    }

    /**
     * Generate hash
     * @param null|string $string
     * @param int $length
     * @return string
     */
    public static function hash($string = null, $length = 32) {
        if (!$string) {
            $string = mt_rand().microtime().mt_rand();
        }

        $hash = hash_hmac('sha256', $string, md5($string));

        if ($length < strlen($hash)) {
            $hash = substr($hash, 0, $length);
        } elseif ($length > strlen($hash)) {
            $hash = str_pad($hash, $length, $hash);
        }

        return $hash;
    }
}