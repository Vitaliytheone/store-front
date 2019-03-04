<?php

namespace sommerce\helpers;

use app\helpers\ArrayHelper;
use common\models\sommerce\Checkouts;
use common\models\sommerce\Packages;

/**
 * Class PaymentsModalHelper
 * @package sommerce\helpers
 */
class PaymentsModalHelper
{
    /**
     * @param Checkouts $checkout
     * @return array
     */
    public static function getSuccessDetails(Checkouts $checkout)
    {
        $detail = reset($checkout->getUserDetails());

        $package = Packages::find()
            ->select('name')
            ->where([
                'id' => ArrayHelper::getValue($detail, 'package_id')
            ])
            ->scalar();

        $order = $checkout->order;

        return [
            'status' => $checkout->status,
            'price' => $checkout->price,
            'details' => ArrayHelper::getValue($detail, 'link'),
            'package' => $package,
            'order_id' => $order->id
        ];
    }

}