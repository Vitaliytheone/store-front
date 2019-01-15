<?php

namespace sommerce\modules\admin\models\search;

use common\models\stores\Stores;
use common\models\stores\StorePaymentMethods;

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
     * @return array
     */
    public function search(): array
    {
        return StorePaymentMethods::find()
            ->where(['store_id' => $this->store->id])
            ->orderBy('position')
            ->all();
    }
}
