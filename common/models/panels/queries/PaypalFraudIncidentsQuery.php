<?php

namespace common\models\panels\queries;


use yii\db\ActiveQuery;
use common\models\panels\PaypalFraudIncidents;

/**
 * Class PaypalFraudIncidentsQuery
 * @package common\models\panels\queries
 */
class PaypalFraudIncidentsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PaypalFraudIncidents[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudIncidents|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}