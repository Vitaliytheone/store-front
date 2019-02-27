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
            'deleted' => Pages::DELETED_NO,
            'visibility' => Pages::VISIBILITY_YES
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
