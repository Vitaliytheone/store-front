<?php

namespace common\models\sommerces\queries;

use common\models\sommerces\InvoiceDetails;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\InvoiceDetails]].
 *
 * @see \common\models\sommerces\InvoiceDetails
 */
class InvoiceDetailsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\sommerces\InvoiceDetails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\InvoiceDetails|array|null
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
