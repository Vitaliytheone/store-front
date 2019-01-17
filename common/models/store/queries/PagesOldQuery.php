<?php

namespace common\models\store\queries;

use common\models\store\PagesOld;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Pages]].
 *
 * @see PagesOld
 */
class PagesOldQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'deleted' => PagesOld::DELETED_NO,
            'visibility' => PagesOld::VISIBILITY_YES
        ]);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\PagesOld[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\PagesOld|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
