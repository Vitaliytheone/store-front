<?php

namespace common\models\panels\queries;


use yii\db\ActiveQuery;
use common\models\panels\PaypalFraudReports;

/**
 * This is the ActiveQuery class for [[PaypalFraudReports]].
 *
 * @see PaypalFraudReports
 */
class PaypalFraudReportsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PaypalFraudReports[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudReports|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}