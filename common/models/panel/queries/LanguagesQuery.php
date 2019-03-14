<?php

namespace common\models\panel\queries;

use common\models\panel\Languages;

/**
 * This is the ActiveQuery class for [[\app\models\panel\Languages]].
 *
 * @see \common\models\panel\Languages
 */
class LanguagesQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['active' => 1]);
    }

    /**
     * {@inheritdoc}
     * @return Languages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Languages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
