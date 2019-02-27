<?php

namespace common\models\sommerce\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Orders]].
 *
 * @see \common\models\sommerce\Orders
 */
class OrdersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Orders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Orders|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
