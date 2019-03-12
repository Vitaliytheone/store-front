<?php

namespace common\models\sommerces\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\StoreAdmins]].
 *
 * @see \common\models\sommerces\StoreAdmins
 */
class StoreAdminsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerces\StoreAdmins[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\StoreAdmins|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
