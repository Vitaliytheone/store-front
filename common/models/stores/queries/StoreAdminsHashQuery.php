<?php

namespace common\models\stores\queries;

/**
 * This is the ActiveQuery class for [[\common\models\stores\StoreAdminsHash]].
 *
 * @see \common\models\stores\StoreAdminsHash
 */
class StoreAdminsHashQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\stores\StoreAdminsHash[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\StoreAdminsHash|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
