<?php

namespace my\helpers;

use common\models\panels\MyVerifiedPaypal;
use common\models\panels\Params;
use common\models\panels\Payments;
use common\models\panels\PaymentsLog;
use common\models\panels\ThirdPartyLog;
use my\components\payments\Paypal;
use yii\helpers\ArrayHelper;
use Yii;

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

    /**
     * Make PayPal payment refund action
     * @param Payments $payment
     * @return bool
     */
    public static function refundPaypalPayment(Payments $payment)
    {
        if ($payment->type != Params::getPaymentPGID(Params::CODE_PAYPAL) || empty($payment->transaction_id)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_REFUND_PAYPAL_PAYMENT, $payment->id, ['payment_attributes' => $payment->attributes], 'required_params_missed');
        }

        $paypal = new Paypal();

        $requestParams = array(
            // Unique identifier of the transaction to be refunded.
            // Character length and limitations: 17 characters except for transactions of the type Order have a character length of 19.
            'TRANSACTIONID' => $payment->transaction_id,
            // Type of refund you are making. Value is:
            // Full — Full refund (default).
            // Partial — Partial refund.
            'REFUNDTYPE' => 'Full',
        );

        $response = $paypal->request('RefundTransaction', $requestParams);

        $logParams = ['payment_model' => $payment->attributes, 'request_params' => $requestParams, 'response' => $response];

        ThirdPartyLog::log(ThirdPartyLog::ITEM_REFUND_PAYPAL_PAYMENT, $payment->id, $logParams, 'refund.response');

        PaymentsLog::log($payment->id, ['RefundTransaction' => $response], array_merge($_GET, $_POST, $_SERVER), ArrayHelper::getValue($_SERVER, 'REMOTE_ADDR','localhost'));

        $ppAck = ArrayHelper::getValue($response,'ACK');
        $ppRefundAmount = ArrayHelper::getValue($response,'TOTALREFUNDEDAMOUNT');

        if ($ppAck !== 'Success' ||
            (int)$ppRefundAmount !== (int)$payment->amount
        ) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_REFUND_PAYPAL_PAYMENT, $payment->id, $logParams, 'refund.error');
            return false;
        }

        return true;
    }

    /**
     * Refund all PayPal verification expired payments
     */
    public static function refundPaypalVerifyExpiredPayments()
    {
        $verificationTime = Yii::$app->params['payment_verification_time'];

        $payments = Payments::find()
            ->andWhere([
                'type' => Params::getPaymentPGID(Params::CODE_PAYPAL),
                'status' => Payments::STATUS_VERIFICATION,
            ])
            ->andWhere(['<', 'date_update', time() - $verificationTime])
            ->all();

        /** @var Payments $payment */
        foreach ($payments as $payment) {
            if (static::refundPaypalPayment($payment)) {
                $payment->status = Payments::STATUS_UNVERIFIED;
                $payment->save(false);
            }
        }
    }
}