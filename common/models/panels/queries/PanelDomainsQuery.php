<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\PanelDomains]].
 *
 * @see \common\models\panels\PanelDomains
 */
class PanelDomainsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\PanelDomains[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\PanelDomains|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
