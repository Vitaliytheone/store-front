<?php

namespace common\models\sommerces\queries;

use common\models\sommerces\Params;

/**
 * This is the ActiveQuery class for [[Params]].
 *
 * @see Params
 */
class ParamsQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Params[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Params|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
