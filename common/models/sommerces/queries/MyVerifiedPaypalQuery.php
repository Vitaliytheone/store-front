<?php

namespace common\models\sommerces\queries;

use common\models\sommerces\MyVerifiedPaypal;
use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[MyVerifiedPaypal]].
 *
 * @see MyVerifiedPaypal
 */
class MyVerifiedPaypalQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MyVerifiedPaypal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MyVerifiedPaypal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
