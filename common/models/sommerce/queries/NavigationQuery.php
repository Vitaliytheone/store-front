<?php

namespace common\models\sommerce\queries;

use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Navigation]].
 *
 * @see \common\models\sommerce\Navigation
 */
class NavigationQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Navigation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Navigation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
