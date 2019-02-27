<?php

namespace common\models\sommerces\queries;


use common\models\sommerces\StorePaymentMethods;
use yii\db\ActiveQuery;
use common\models\sommerces\Stores;

/**
 * Class StorePaymentMethodsQuery
 * @package common\models\sommerces\queries
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
     * @return \common\models\sommerces\PaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\PaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}