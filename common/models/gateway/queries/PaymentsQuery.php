<?php
namespace common\models\gateway\queries;

use yii\db\ActiveQuery;
use common\models\gateway\Payments;

/**
 * This is the ActiveQuery class for [[Payments]].
 *
 * @see Payments
 */
class PaymentsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Payments[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Payments|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}