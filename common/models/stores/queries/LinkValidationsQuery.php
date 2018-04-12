<?php
namespace common\models\stores\queries;

use yii\db\ActiveQuery;
use common\models\stores\LinkValidations;

/**
 * This is the ActiveQuery class for [[LinkValidations]].
 *
 * @see LinkValidations
 */
class LinkValidationsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return LinkValidations[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return LinkValidations|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}