<?php
namespace common\helpers;

use common\models\panels\Params;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentHelper
 * @package common\helpers
 */
class PaymentHelper {

    public const TYPE_PAYPAL = 1;
    public const TYPE_PERFECT_MONEY = 2;
    public const TYPE_WEBMONEY = 3;
    public const TYPE_BITCOIN = 4;
    public const TYPE_TWO_CHECKOUT = 5;
    public const TYPE_COINPAYMENTS = 6;

    /**
     * Get type id by payment params code
     * @param string $code
     * @return mixed
     */
    public static function getTypeByCode(string $code)
    {
        return ArrayHelper::getValue([
            Params::CODE_PAYPAL => static::TYPE_PAYPAL,
            Params::CODE_PERFECT_MONEY => static::TYPE_PERFECT_MONEY,
            Params::CODE_WEBMONEY => static::TYPE_WEBMONEY,
            Params::CODE_BITCOIN => static::TYPE_BITCOIN,
            Params::CODE_TWO_CHECKOUT => static::TYPE_TWO_CHECKOUT,
            Params::CODE_COINPAYMENTS => static::TYPE_COINPAYMENTS,
        ], $code);
    }

    /**
     * Get code id by payment type
     * @param integer $type
     * @return mixed
     */
    public static function getCodeByType(int $type)
    {
        return ArrayHelper::getValue([
            static::TYPE_PAYPAL => Params::CODE_PAYPAL,
            static::TYPE_PERFECT_MONEY => Params::CODE_PERFECT_MONEY,
            static::TYPE_WEBMONEY => Params::CODE_WEBMONEY,
            static::TYPE_BITCOIN => Params::CODE_BITCOIN,
            static::TYPE_TWO_CHECKOUT => Params::CODE_TWO_CHECKOUT,
            static::TYPE_COINPAYMENTS => Params::CODE_COINPAYMENTS,
        ], $type);
    }
}