<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\MyCustomersHash;

/**
 * This is the ActiveQuery class for [[MyCustomersHash]].
 *
 * @see MyCustomersHash
 */
class MyCustomersHashQuery extends ActiveQuery
{
    public function remember()
    {
        return $this->andWhere([
            'remember' => 1
        ]);
    }

    public function notRemember()
    {
        return $this->andWhere([
            'remember' => 0
        ]);
    }

    public function superUser()
    {
        return $this->andWhere([
            'super_user' => 1
        ]);
    }

    public function notSuperUser()
    {
        return $this->andWhere([
            'super_user' => 0
        ]);
    }

    /**
     * @inheritdoc
     * @return MyCustomersHash[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MyCustomersHash|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
