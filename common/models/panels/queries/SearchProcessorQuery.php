<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\my\models\searchProcessor]].
 *
 * @see \my\models\searchProcessor
 */
class SearchProcessorQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \my\models\searchProcessor[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \my\models\searchProcessor|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
