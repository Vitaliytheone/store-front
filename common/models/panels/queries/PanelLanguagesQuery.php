<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\PanelLanguages;

/**
 * This is the ActiveQuery class for [[\app\models\panels\PanelLanguages]].
 *
 * @see \app\models\panels\PanelLanguages
 */
class PanelLanguagesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PanelLanguages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PanelLanguages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
