<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerces\Stores;
use common\models\sommerces\StorePaymentMethods;

/**
 * Class PaymentsSettingsSearch
 * @package sommerce\modules\admin\models\search
 */
class PaymentsSettingsSearch
{
    /** @var Stores */
    private $store;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->store = $store;
    }

    /**
     * @return \common\models\sommerces\queries\StorePaymentMethodsQuery
     */
    public function search()
    {
        return StorePaymentMethods::find()
            ->where(['store_id' => $this->store->id])
            ->orderBy('name')
            ->all();
    }
}
