<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\ThirdPartyLog]].
 *
 * @see \common\models\panels\ThirdPartyLog
 */
class ThirdPartyLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\ThirdPartyLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\ThirdPartyLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
