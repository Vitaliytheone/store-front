<?php

namespace common\models\gateway\queries;

use yii\db\ActiveQuery;
use common\models\gateway\ThemesFiles;

/**
 * This is the ActiveQuery class for [[ThemesFiles]].
 *
 * @see ThemesFiles
 */
class ThemesFilesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ThemesFiles[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ThemesFiles|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
