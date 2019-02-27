<?php

namespace common\models\store\queries;

use yii\db\ActiveQuery;
use common\models\store\CustomThemes;

/**
 * This is the ActiveQuery class for [[CustomThemes]].
 *
 * @see CustomThemes
 */
class CustomThemesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CustomThemes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CustomThemes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
