<?php

namespace common\models\sommerce\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Files]].
 *
 * @see \common\models\sommerce\Files
 */
class FilesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Files[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Files|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
