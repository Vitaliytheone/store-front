<?php
namespace common\models\store\queries;

use yii\db\ActiveQuery;
use common\models\store\Suborders;

/**
 * This is the ActiveQuery class for [[Suborders]].
 *
 * @see Suborders
 */
class SubordersQuery extends ActiveQuery
{
    public function notCompleted()
    {
        return $this->andWhere('status <> ' . Suborders::STATUS_COMPLETED);
    }

    /**
     * @inheritdoc
     * @return Suborders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Suborders|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
