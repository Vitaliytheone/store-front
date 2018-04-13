<?php

namespace my\helpers;

use common\models\panels\MyVerifiedPaypal;
use common\models\panels\Payments;

/**
 * Class PaymentsHelper
 * @package my\helpers
 */
class PaymentsHelper {

    /**
     * Return is customer has prolonged panels or already passed the PayPal payer email check
     * @param Payments $payment
     * @param $payerId string|int
     * @param $payerEmail string
     * @return bool
     */
    public static function validatePaypalPayment(Payments $payment, $payerId, $payerEmail)
    {
        if (!$invoice = $payment->invoice) {
            return false;
        }

        if (!$customer = $invoice->customer) {
            return false;
        }

        if ($customer->hasProlongedPanels()) {
            return true;
        }

        if (!filter_var($payerEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $verifiedEmail =  MyVerifiedPaypal::findOne([
            'paypal_payer_id' => trim($payerId),
            'paypal_payer_email' => mb_strtolower(trim($payerEmail)),
            'verified' => MyVerifiedPaypal::STATUS_VERIFIED
        ]);

        return (bool)$verifiedEmail;
    }
}