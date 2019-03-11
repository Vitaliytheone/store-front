<?php

namespace common\models\sommerces\queries;

use  \yii\db\ActiveQuery;
use \common\models\sommerces\StoreDefaultMessages;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\StoreDefaultMessages]].
 *
 * @see StoreDefaultMessages
 */
class StoreDefaultMessagesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return StoreDefaultMessages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StoreDefaultMessages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
