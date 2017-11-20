<?php

namespace common\models\stores\queries;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;

/**
 * This is the ActiveQuery class for [[\common\models\stores\PaymentMethods]].
 *
 * @see \common\models\stores\PaymentMethods
 */
class PaymentMethodsQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'active' => PaymentMethods::ACTIVE_ENABLED
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
