<?php

namespace common\models\store\queries;

use \common\models\store\Messages;
use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\store\Messages]].
 *
 * @see Messages
 */
class MessagesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Messages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Messages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
