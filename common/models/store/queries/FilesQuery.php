<?php

namespace common\models\store\queries;

/**
 * This is the ActiveQuery class for [[\common\models\store\Files]].
 *
 * @see \common\models\store\Files
 */
class FilesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\store\Files[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Files|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}