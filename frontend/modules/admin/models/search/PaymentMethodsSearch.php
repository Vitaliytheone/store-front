<?php

namespace frontend\modules\admin\models\search;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\stores\PaymentMethods;

/**
 * Class PaymentMethodsSearch
 * @package frontend\modules\admin\models\searches
 */
class PaymentMethodsSearch extends PaymentMethods
{
    /**
     * Return payments methods list item data
     * @param $field
     * @return mixed
     */
    public function getMethodsListItemData($field)
    {
        $method = $this->method;

        $methodItemsData = [
            self::METHOD_PAYPAL => [
                'icon' => '/img/paypal.png',
                'title' => Yii::t('admin', 'settings.payments_method_paypal'),
                'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
            ],
            self::METHOD_2CHECKOUT => [
                'icon' => '/img/2checkout.png',
                'title' => Yii::t('admin', 'settings.payments_method_2checkout'),
                'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
            ],
            self::METHOD_BITCOIN => [
                'icon' => '/img/bitcoin.png',
                'title' => Yii::t('admin', 'settings.payments_method_bitcoin'),
                'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
            ],
        ];

        return ArrayHelper::getValue($methodItemsData, "$method.$field", $field);
    }
}