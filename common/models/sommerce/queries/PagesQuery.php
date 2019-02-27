<?php

namespace common\models\sommerce\queries;

use common\models\sommerce\Pages;
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
     * @return \common\models\sommerce\Pages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Pages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
