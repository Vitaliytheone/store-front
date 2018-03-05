<?php

namespace common\models\store\queries;

/**
 * This is the ActiveQuery class for [[\common\models\store\ActivityLog]].
 *
 * @see \common\models\store\ActivityLog
 */
class ActivityLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\store\ActivityLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\ActivityLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
