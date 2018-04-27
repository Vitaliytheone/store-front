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
            self::METHOD_PAYPAL => [
                'icon' => '/img/pg/paypal.png',
                'title' => Yii::t('admin', 'settings.payments_method_paypal'),
            ],
            self::METHOD_2CHECKOUT => [
                'icon' => '/img/pg/2checkout.png',
                'title' => Yii::t('admin', 'settings.payments_method_2checkout'),
            ],
            self::METHOD_COINPAYMENTS => [
                'icon' => '/img/pg/coinpayments.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_COINPAYMENTS => [
                'icon' => '/img/pg/coinpayments.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],

            self::METHOD_WEBMONEY => [
                'icon' => '/img/pg/webmoney.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_YANDEX_MONEY => [
                'icon' => '/img/pg/yandex_money.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_FREE_KASSA => [
                'icon' => '/img/pg/free_kassa.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_PAYTR => [
                'icon' => '/img/pg/paytr.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_PAYWANT => [
                'icon' => '/img/pg/paywant.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_PAGSEGURU => [
                'icon' => '/img/pg/pageseguro.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
            self::METHOD_BILLPLZ => [
                'icon' => '/img/pg/billplz.png',
                'title' => Yii::t('admin', 'settings.payments_method_coinpayments'),
            ],
        ];

        return ArrayHelper::getValue($methodItemsData, "$method.$methodName", $methodName);
    }
}