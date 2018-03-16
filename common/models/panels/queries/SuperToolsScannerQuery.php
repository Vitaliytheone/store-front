<?php

namespace common\models\panels\queries;

/**
 * Class SuperToolsScannerQuery
 * @package common\models\panels\queries
 */
class SuperToolsScannerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperToolsScanner[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\SuperToolsScanner|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
