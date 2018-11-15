<?php

namespace common\models\panel\queries;

use yii\db\ActiveQuery;
use common\models\panel\Bonuses;

/**
 * This is the ActiveQuery class for [[Bonuses]].
 *
 * @see Bonuses
 */
class BonusesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Bonuses[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Bonuses|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
