<?php

namespace common\models\sommerce\queries;

use \common\models\sommerce\Languages;
use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Languages]].
 *
 * @see Languages
 */
class LanguagesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Languages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Languages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
