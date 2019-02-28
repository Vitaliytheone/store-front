<?php
namespace common\models\sommerces\queries;

use yii\db\ActiveQuery;
use common\models\sommerces\SuperAdminToken;

/**
 * This is the ActiveQuery class for [[SuperAdminToken]].
 *
 * @see SuperAdminToken
 */
class SuperAdminTokenQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SuperAdminToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SuperAdminToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
