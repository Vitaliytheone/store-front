<?php

namespace sommerce\helpers;


use common\models\sommerce\Checkouts;
use common\models\sommerce\Packages;
use common\models\sommerce\Payments;
use common\models\sommerces\Stores;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentsModalHelper
 * @package sommerce\helpers
 */
class PaymentsModalHelper
{
    /**
     * @var Stores
     */
    protected $store;

    /**
     * @return Stores
     */
    public function getStore(): Stores
    {
        return $this->store;
    }

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->store = $store;
    }

    /**
     * @param Checkouts $checkout
     * @return bool|array
     */
    public function getSuccessDetails(Checkouts $checkout)
    {
        $detail = ArrayHelper::getValue($checkout->getDetails(), 0);
        $order = $checkout->order;

        $paymentStatus = Payments::find()
            ->select('status')
            ->where(['checkout_id' => $checkout->id])
            ->scalar();

        if (!$paymentStatus || !$detail || !$order)  {
            return false;
        }

        $package = Packages::find()
            ->select('name')
            ->where([
                'id' => ArrayHelper::getValue($detail, 'package_id')
            ])
            ->scalar();



        return [
            'status' => ArrayHelper::getValue(Payments::getStatuses(), $paymentStatus),
            'price' => PriceHelper::getPrice($checkout->price, $this->getStore()->currency),
            'details' => ArrayHelper::getValue($detail, 'link'),
            'package' => $package,
            'order_id' => $order->id
        ];
    }

}