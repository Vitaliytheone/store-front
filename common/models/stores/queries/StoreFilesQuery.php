<?php

namespace common\models\stores\queries;

/**
 * This is the ActiveQuery class for [[\common\models\stores\StoreFiles]].
 *
 * @see \common\models\stores\StoreFiles
 */
class StoreFilesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\stores\StoreFiles[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\StoreFiles|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
