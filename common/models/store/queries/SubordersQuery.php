<?php

namespace common\models\store\queries;

/**
 * This is the ActiveQuery class for [[\common\models\store\Suborders]].
 *
 * @see \common\models\store\Suborders
 */
class SubordersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\store\Suborders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Suborders|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
