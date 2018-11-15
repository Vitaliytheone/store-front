<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\PaymentMethods;

/**
 * This is the ActiveQuery class for [[PaymentMethods]].
 *
 * @see PaymentMethods
 */
class PaymentMethodsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
