<?php

namespace common\models\sommerce\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Payments]].
 *
 * @see \common\models\sommerce\Payments
 */
class PaymentsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Payments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Payments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
