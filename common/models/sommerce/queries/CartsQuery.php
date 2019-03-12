<?php

namespace common\models\sommerce\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Carts]].
 *
 * @see \common\models\sommerce\Carts
 */
class CartsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Carts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Carts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
