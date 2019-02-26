<?php

namespace common\models\stores\queries;


use common\models\stores\StorePaymentMethods;
use yii\db\ActiveQuery;
use common\models\stores\Stores;

/**
 * Class StorePaymentMethodsQuery
 * @package common\models\stores\queries
 */
class StorePaymentMethodsQuery extends ActiveQuery
{
    /**
     * @return StorePaymentMethodsQuery
     */
    public function active()
    {
        return $this->andWhere([
            'visibility' => StorePaymentMethods::VISIBILITY_ENABLED
        ]);
    }

    /**
     * @param Stores $store
     * @return $this
     */
    public function store(Stores $store)
    {
        return $this->andWhere([
            'store_id' => $store->id
        ]);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\PaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\PaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}