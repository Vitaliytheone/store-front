<?php

namespace common\models\stores\queries;

/**
 * This is the ActiveQuery class for [[\common\models\stores\Stores]].
 *
 * @see \common\models\stores\Stores
 */
class StoresQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\stores\Stores[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\Stores|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
