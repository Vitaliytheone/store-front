<?php
namespace common\models\gateways\queries;

use yii\db\ActiveQuery;
use common\models\gateways\Sites;

/**
 * This is the ActiveQuery class for [[Sites]].
 *
 * @see Sites
 */
class SitesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Sites[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Sites|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}