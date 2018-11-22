<?php

namespace common\models\panels\queries;


use yii\db\ActiveQuery;
use common\models\panels\PaypalFraudResponse;

/**
 * This is the ActiveQuery class for [[PaypalFraudResponse]].
 *
 * @see PaypalFraudResponse
 */
class PaypalFraudResponseQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PaypalFraudResponse[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudResponse|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
