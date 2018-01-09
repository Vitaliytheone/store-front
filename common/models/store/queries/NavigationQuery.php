<?php

namespace common\models\store\queries;

use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\store\Navigation]].
 *
 * @see \common\models\store\Navigation
 */
class NavigationQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\store\Navigation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Navigation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
