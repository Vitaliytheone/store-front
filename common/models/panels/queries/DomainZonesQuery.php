<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\DomainZones]].
 *
 * @see \common\models\panels\DomainZones
 */
class DomainZonesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\DomainZones[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\DomainZones|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
