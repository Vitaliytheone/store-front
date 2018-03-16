<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[SuperCreditsLog]].
 *
 * @see \common\models\panels\SuperCreditsLog
 */
class SuperCreditsLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperCreditsLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperCreditsLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
