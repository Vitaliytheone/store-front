<?php

namespace common\models\store\queries;

use \yii\db\ActiveQuery;
use \common\models\store\Languages;

/**
 * This is the ActiveQuery class for [[\common\models\store\Languages]].
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
