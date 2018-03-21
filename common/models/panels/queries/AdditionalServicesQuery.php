<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\AdditionalServices]].
 *
 * @see \common\models\panels\AdditionalServices
 */
class AdditionalServicesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\AdditionalServices[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\AdditionalServices|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
