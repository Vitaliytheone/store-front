<?php

namespace common\models\stores\queries;

/**
 * This is the ActiveQuery class for [[\common\models\stores\StoresSendOrders]].
 *
 * @see \common\models\stores\StoresSendOrders
 */
class StoresSendOrdersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\stores\StoresSendOrders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\StoresSendOrders|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
