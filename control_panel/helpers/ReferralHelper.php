<?php
namespace control_panel\helpers;

use common\models\panels\Customers;
use common\models\panels\ReferralVisits;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;

/**
 * Class ReferralHelper
 * @package control_panel\helpers
 */
class ReferralHelper {

    const REF_DOMAIN = '.perfectpanel.com';
    const REDIRECT_DOMAIN = 'https://perfectpanel.com';
    const REF_KEY = 'ref';

    protected static function getDomain()
    {
        return YII_ENV_DEV ? '.' . $_SERVER['HTTP_HOST'] : static::REF_DOMAIN;
    }

    /**
     * Get redirect url after join uses ref link
     * @return string
     */
    public static function redirectUrl($url)
    {
        return YII_ENV_DEV ? $url : static::REDIRECT_DOMAIN;
    }

    /**
     * Visit by user referral link
     * @param Customers $customer
     * @return void
     */
    public static function visit($customer)
    {
        if (!static::has()) {
            $visit = new ReferralVisits();
            $visit->customer_id = $customer->id;
            $visit->http_referer = ArrayHelper::getValue($_SERVER, 'HTTP_REFERER', ' ');
            $visit->request_data = json_encode($_SERVER, JSON_PRETTY_PRINT);
            $visit->user_agent = ArrayHelper::getValue($_SERVER, 'HTTP_USER_AGENT');
            $visit->ip = UserHelper::ip();
            $visit->save(false);
        }

        static::add($customer->referral_link);
    }

    /**
     * Has ref
     * @return bool
     */
    public static function has()
    {
        $cookies = Yii::$app->request->cookies;

        return $cookies->has(static::REF_KEY);
    }

    /**
     * Set ref value
     * @param string $code
     */
    public static function add($code)
    {
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => static::REF_KEY,
            'value' => $code,
            'domain' => static::getDomain(),
            'expire' => time() + Yii::$app->params['referral_link_expiry'] * (60 * 60 *24) // В днях
        ]));
    }

    /**
     * Remove ref value
     */
    public static function remove()
    {
        $cookies = Yii::$app->response->cookies;

        $cookies->remove(static::REF_KEY);
    }

    /**
     * Get ref value
     * @return mixed|null
     */
    public static function get()
    {
        if (!static::has()) {
            return null;
        }

        return Yii::$app->request->cookies->getValue(static::REF_KEY);
    }
}
