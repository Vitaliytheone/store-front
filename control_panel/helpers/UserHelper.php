<?php
namespace control_panel\helpers;

use Yii;
use yii\web\Cookie;

/**
 * Class UserHelper
 * @package control_panel\helpers
 */
class UserHelper {

    const HASH_KEY = 'hash';
    const AUTH_DURATION = 3600 * 24 * 30;

    /**
     * Get user ip
     * @return string
     */
    public static function ip()
    {
        $ip = null;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Get user hash value
     * @return string|null
     */
    public static function getHash()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }

        $session = Yii::$app->session;

        if ($session->has(static::HASH_KEY)) {
            return (string)$session->get(static::HASH_KEY);
        }

        $cookies = Yii::$app->request->cookies;
        if ($cookies->has(static::HASH_KEY)) {
            return (string)$cookies->getValue(static::HASH_KEY);
        }
    }

    /**
     * Set user hash value
     * @param string $hash
     * @param bool $remember
     * @return string|null
     */
    public static function setHash($hash, $remember = false)
    {
        $session = Yii::$app->session;
        $session->set(static::HASH_KEY, $hash);

        if ($remember) {
            $cookies = Yii::$app->response->cookies;

            $cookies->add(new Cookie([
                'name' => static::HASH_KEY,
                'value' => $hash,
                'expire' => time() + static::AUTH_DURATION
            ]));
        }
    }
}


