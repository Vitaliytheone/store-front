<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\panels\Payments]].
 *
 * @see \common\models\panels\Payments
 */
class PaymentsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\Payments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\Payments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}