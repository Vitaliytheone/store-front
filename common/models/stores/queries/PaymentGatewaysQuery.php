<?php

namespace common\models\stores\queries;

use \yii\db\ActiveQuery;
use \common\models\stores\PaymentGateways;

/**
 * This is the ActiveQuery class for [[PaymentGateways]].
 *
 * @see PaymentGateways
 */
class PaymentGatewaysQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PaymentGateways[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentGateways|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
