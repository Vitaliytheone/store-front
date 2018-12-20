<?php

namespace sommerce\modules\admin\models\search;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;

/**
 * Class PaymentMethodsSearch
 * @package sommerce\modules\admin\models\searches
 */
class PaymentMethodsSearch extends PaymentMethods
{
    /**
     * Return payments methods list item data
     * @param $methodName
     * @return mixed
     */
    public function getViewData($methodName)
    {
        $method = $this->method;

        $methodItemsData = [
            PaymentMethods::METHOD_PAYPAL => [
                'icon' => '/img/pg/paypal.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_PAYPAL),
            ],
            PaymentMethods::METHOD_PAYPAL_STANDARD => [
                'icon' => '/img/pg/paypal.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_PAYPAL_STANDARD),
            ],
            PaymentMethods::METHOD_2CHECKOUT => [
                'icon' => '/img/pg/2checkout.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_2CHECKOUT),
            ],
            PaymentMethods::METHOD_COINPAYMENTS => [
                'icon' => '/img/pg/coinpayments.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_COINPAYMENTS),
            ],
            PaymentMethods::METHOD_WEBMONEY => [
                'icon' => '/img/pg/webmoney.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_WEBMONEY),
            ],
            PaymentMethods::METHOD_YANDEX_MONEY => [
                'icon' => '/img/pg/yandex_money.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_YANDEX_MONEY),
            ],
            PaymentMethods::METHOD_YANDEX_CARDS => [
                'icon' => '/img/pg/yandex_money.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_YANDEX_CARDS),
            ],
            PaymentMethods::METHOD_FREE_KASSA => [
                'icon' => '/img/pg/free_kassa.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_FREE_KASSA),
            ],
            PaymentMethods::METHOD_PAYTR => [
                'icon' => '/img/pg/paytr.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_PAYTR),
            ],
            PaymentMethods::METHOD_PAYWANT => [
                'icon' => '/img/pg/paywant.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_PAYWANT),
            ],
            PaymentMethods::METHOD_PAGSEGURO => [
                'icon' => '/img/pg/pagseguro.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_PAGSEGURO),
            ],
            PaymentMethods::METHOD_BILLPLZ => [
                'icon' => '/img/pg/billplz.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_BILLPLZ),
            ],
            PaymentMethods::METHOD_AUTHORIZE => [
                'icon' => '/img/pg/authorize.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_AUTHORIZE),
            ],
            PaymentMethods::METHOD_STRIPE => [
                'icon' => '/img/pg/stripe_logo.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_STRIPE),
                'style' => 'margin:10px;'
            ],
            PaymentMethods::METHOD_MERCADOPAGO => [
                'icon' => '/img/pg/mercado_pago.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_MERCADOPAGO),
            ],
            PaymentMethods::METHOD_MOLLIE => [
                'icon' => '/img/pg/mollie.png',
                'title' => PaymentMethods::getMethodName(PaymentMethods::METHOD_MOLLIE),
            ],
        ];

        return ArrayHelper::getValue($methodItemsData, "$method.$methodName", $methodName);
    }
}