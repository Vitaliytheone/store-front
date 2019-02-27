<?php

namespace sommerce\components\payments\methods;

use common\helpers\SiteHelper;
use common\models\sommerce\Checkouts;
use common\models\sommerces\Stores;
use yii\helpers\ArrayHelper;
use common\models\sommerces\StorePaymentMethods;

/**
 * Class Yandexcards
 * @package app\components\payments\methods
 */
class Yandexcards extends Yandexmoney
{

    /**
     * Checkout Yandex Card method
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        return static::returnForm($this->getFrom(), [
            'receiver' => ArrayHelper::getValue($paymentMethodOptions, 'wallet_number'),
            'label' => $checkout->id,
            'formcomment' => static::getDescription($checkout->id),
            'short-dest' => static::getDescription($checkout->id),
            'targets' => static::getDescription($checkout->id),
            'sum' => $checkout->price,
            'quickpay-form' => 'shop',
            'paymentType' => 'AC',
            'successURL' => SiteHelper::hostUrl($store->ssl) . '/cart'
        ]);
    }

}