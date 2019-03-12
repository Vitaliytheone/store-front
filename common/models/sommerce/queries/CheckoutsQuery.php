<?php
namespace common\models\sommerce\queries;

use yii\db\ActiveQuery;
use common\models\sommerce\Checkouts;

/**
 * This is the ActiveQuery class for [[Checkouts]].
 *
 * @see Checkouts
 */
class CheckoutsQuery extends ActiveQuery
{
    public function abandoned()
    {
        return $this->andWhere([
            'status' => Checkouts::STATUS_PENDING
        ])
        ->andWhere(['<', 'created_at', (time() - (48 * 60 * 60))]);
    }

    /**
     * @inheritdoc
     * @return Checkouts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Checkouts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
