<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\PaymentGateway;

/**
 * This is the ActiveQuery class for [[PaymentGateway]].
 *
 * @see PaymentGateway
 */
class PaymentGatewayQuery extends ActiveQuery
{
    public function active()
    {
        return $this->where([
            'pid' => -1,
            'visibility' => 1
        ])->orderBy(['position' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     * @return PaymentGateway[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentGateway|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
