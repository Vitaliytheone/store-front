<?php
namespace common\models\stores\queries;

use yii\db\ActiveQuery;
use common\models\stores\Stores;

/**
 * This is the ActiveQuery class for [[Stores]].
 *
 * @see Stores
 */
class StoresQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
                'status' => Stores::STATUS_ACTIVE
            ])
            ->andWhere('db_name IS NOT NULL');
    }

    /**
     * @inheritdoc
     * @return Stores[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Stores|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
