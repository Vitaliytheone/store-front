<?php

namespace common\models\sommerces\queries;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\Payments]].
 *
 * @see \common\models\sommerces\Payments
 */
class PaymentsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerces\Payments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\Payments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}