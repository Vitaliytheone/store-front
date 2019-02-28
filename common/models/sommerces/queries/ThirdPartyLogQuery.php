<?php

namespace common\models\sommerces\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\ThirdPartyLog]].
 *
 * @see \common\models\sommerces\ThirdPartyLog
 */
class ThirdPartyLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerces\ThirdPartyLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\ThirdPartyLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
