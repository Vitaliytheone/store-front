<?php

namespace common\models\store\queries;

use common\models\store\Pages;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Pages]].
 *
 * @see Pages
 */
class PagesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'visibility' => Pages::VISIBILITY_ON,
        ]);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Pages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Pages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
