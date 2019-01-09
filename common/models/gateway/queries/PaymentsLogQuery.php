<?php
namespace common\models\gateway\queries;

use yii\db\ActiveQuery;
use common\models\gateway\PaymentsLog;

/**
 * This is the ActiveQuery class for [[PaymentsLog]].
 *
 * @see PaymentsLog
 */
class PaymentsLogQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PaymentsLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PaymentsLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}