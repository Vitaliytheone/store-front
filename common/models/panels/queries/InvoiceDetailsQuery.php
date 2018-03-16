<?php

namespace common\models\panels\queries;

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
}
