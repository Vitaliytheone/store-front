<?php
namespace sommerce\modules\admin\helpers;

use common\models\stores\PaymentGateways;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;

/**
 * Class PaymentsHelper
 * @package sommerce\modules\admin\helpers
 */
class PaymentsHelper
{

    /**
     * Update store payment method list by available payment gateways
     * @param $store Stores
     */
    public static function updateStorePaymentMethods(Stores $store){

        $pgList = PaymentGateways::find()->all();

        /** @var PaymentGateways $pg */
        foreach ($pgList as $pg)
        {
            if ($pg->visibility === $pg::GATEWAY_PUBLIC && $pg->isCurrencySupported($store->currency) &&
                !PaymentMethods::findOne(['store_id' => $store->id, 'method' => $pg->method])) {
                $paymentMethod = new PaymentMethods();
                $paymentMethod->method = $pg->method;
                $paymentMethod->store_id = $store->id;
                $paymentMethod->active = PaymentMethods::ACTIVE_DISABLED;
                $paymentMethod->save(false);
            }
        }
    }
}