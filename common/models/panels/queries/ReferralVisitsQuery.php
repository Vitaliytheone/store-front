<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\ReferralVisits]].
 *
 * @see \common\models\panels\ReferralVisits
 */
class ReferralVisitsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\ReferralVisits[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\ReferralVisits|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
