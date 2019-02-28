<?php
namespace common\models\sommerce\queries;

use yii\db\ActiveQuery;
use common\models\sommerce\Blocks;

/**
 * This is the ActiveQuery class for [[Blocks]].
 *
 * @see Blocks
 */
class BlocksQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Blocks[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Blocks|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
