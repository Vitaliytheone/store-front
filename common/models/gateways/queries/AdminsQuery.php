<?php
namespace common\models\gateways\queries;

use yii\db\ActiveQuery;
use common\models\gateways\Admins;

/**
 * This is the ActiveQuery class for [[Admins]].
 *
 * @see Admins
 */
class AdminsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Admins[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Admins|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}