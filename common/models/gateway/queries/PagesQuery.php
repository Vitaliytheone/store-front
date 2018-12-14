<?php

namespace common\models\gateway\queries;

use yii\db\ActiveQuery;
use common\models\gateway\Pages;

/**
 * This is the ActiveQuery class for [[Pages]].
 *
 * @see Pages
 */
class PagesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Pages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Pages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
