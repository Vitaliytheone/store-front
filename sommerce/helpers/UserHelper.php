<?php
namespace sommerce\helpers;

use Yii;
use yii\web\Cookie;

/**
 * Class UserHelper
 */
class UserHelper {

    const CART_KEY = 'cart_id';
    const CART_DURATION = 2592000;

    /**
     * Add cart key value
     * @param string $key
     */
    public static function addCartKey($key)
    {
        $keys = static::getCartKeys();
        $keys[] = $key;
        static::setCartKeys($keys);
    }

    /**
     * Remove cart key value
     * @param string $key
     */
    public static function removeCartKey($key)
    {
        $keys = static::getCartKeys();

        if (in_array($key, $keys)) {
            unset($keys[array_search($key, $keys)]);
            static::setCartKeys($keys);
        }
    }

    /**
     * Set user cart keys
     * @param array $keys
     * @return array
     */
    public static function setCartKeys($keys)
    {
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => static::CART_KEY,
            'value' => $keys,
            'expire' => time() + static::CART_DURATION
        ]));
    }

    /**
     * Get user cart keys
     * @return array
     */
    public static function getCartKeys()
    {
        $cookies = Yii::$app->request->cookies;
        if ($cookies->has(static::CART_KEY)) {
            return (array)$cookies->getValue(static::CART_KEY);
        }

        return [];
    }

    /**
     * Flush user cart
     */
    public static function flushCart()
    {
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => static::CART_KEY,
            'value' => [],
            'expire' => time() + static::CART_DURATION
        ]));
    }
}