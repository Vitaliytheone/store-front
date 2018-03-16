<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\SearchProcessor]].
 *
 * @see \common\models\panels\SearchProcessor
 */
class SearchProcessorQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\SearchProcessor[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\SearchProcessor|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
