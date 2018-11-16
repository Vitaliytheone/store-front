<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\PaymentMethodsCurrency;

/**
 * This is the ActiveQuery class for [[PaymentMethodsCurrency]].
 *
 * @see PaymentMethodsCurrency
 */
class PaymentMethodsCurrencyQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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
}