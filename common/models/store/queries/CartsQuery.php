<?php

namespace common\models\store\queries;

/**
 * This is the ActiveQuery class for [[\common\models\store\Carts]].
 *
 * @see \common\models\store\Carts
 */
class CartsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\store\Carts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Carts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
