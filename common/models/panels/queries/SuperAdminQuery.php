<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\SuperAdmin]].
 *
 * @see \common\models\panels\SuperAdmin
 */
class SuperAdminQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperAdmin[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperAdmin|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
