<?php

namespace common\models\stores\queries;


use yii\db\ActiveQuery;
use common\models\stores\PaymentMethodsCurrency;

/**
 * Class PaymentMethodsCurrencyQuery
 * @package common\models\stores\queries
 */
class PaymentMethodsCurrencyQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return PaymentMethodsCurrency[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PaymentMethodsCurrency|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return ActiveQuery
     */
    public function active()
    {
        return parent::andFilterWhere(['hidden' => PaymentMethodsCurrency::NOT_HIDDEN]);
    }

    /**
     * @return ActiveQuery
     */
    public function notActive()
    {
        return parent::andFilterWhere(['hidden' => PaymentMethodsCurrency::HIDDEN]);
    }
}