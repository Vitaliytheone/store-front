<?php
namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\SenderLog;

/**
 * This is the ActiveQuery class for [[SenderLog]].
 *
 * @see SenderLog
 */
class SenderLogQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SenderLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SenderLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}