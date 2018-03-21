<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\SuperTasks]].
 *
 * @see \common\models\panels\SuperTasks
 */
class SuperTasksQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperTasks[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperTasks|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
