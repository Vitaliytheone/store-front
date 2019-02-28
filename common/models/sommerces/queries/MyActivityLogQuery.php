<?php

namespace common\models\sommerces\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\MyActivityLog]].
 *
 * @see \common\models\sommerces\MyActivityLog
 */
class MyActivityLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerces\MyActivityLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\MyActivityLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
