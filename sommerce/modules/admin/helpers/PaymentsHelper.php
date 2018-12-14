<?php
namespace sommerce\modules\admin\helpers;


use common\models\stores\PaymentMethods;
use common\models\stores\StorePaymentMethods;
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
    public static function updateStorePaymentMethods(Stores $store)
    {
        $paymentMethodsList = PaymentMethods::find()->all();

        /** @var PaymentMethods $pm */
        foreach ($paymentMethodsList as $pm)
        {
            if ($pm->isCurrencySupported($store->currency) && !StorePaymentMethods::findOne(['store_id' => $store->id, 'method_id' => $pm->id])) {
                $paymentMethod = new StorePaymentMethods();
                $paymentMethod->method_id = $pm->id;
                $paymentMethod->store_id = $store->id;
                $paymentMethod->visibility = PaymentMethods::ACTIVE_DISABLED;
                $paymentMethod->save(false);
            }
        }
    }
}