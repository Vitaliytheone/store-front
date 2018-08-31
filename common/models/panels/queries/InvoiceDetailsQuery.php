<?php

namespace common\models\panels\queries;

use common\models\panels\InvoiceDetails;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\panels\InvoiceDetails]].
 *
 * @see \common\models\panels\InvoiceDetails
 */
class InvoiceDetailsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\panels\InvoiceDetails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\InvoiceDetails|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return ActiveQuery
     */
    public function orders(): ActiveQuery
    {
        return $this->andWhere(['item' => InvoiceDetails::getOrdersItem()]);
    }
}
