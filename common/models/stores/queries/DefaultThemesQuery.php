<?php

namespace common\models\stores\queries;

use yii\db\ActiveQuery;
use common\models\stores\DefaultThemes;

/**
 * This is the ActiveQuery class for [[DefaultThemes]].
 *
 * @see DefaultThemes
 */
class DefaultThemesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DefaultThemes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DefaultThemes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
