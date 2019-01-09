<?php

namespace sommerce\components\payments\methods;

use Yii;
use common\models\stores\PaymentMethods;
use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\store\Checkouts;
use yii\helpers\ArrayHelper;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Payment;

/**
 * Class Mollie
 * @package app\components\payments\methods
 */
class Mollie extends BasePayment
{

    protected $_method = PaymentMethods::METHOD_MOLLIE;


    /**
     * Create checkout and redirect to Mollie pay site
     *
     * @inheritdoc
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        $amount = number_format((float)$checkout->price, 2, '.', '');

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey(ArrayHelper::getValue($paymentMethodOptions, 'secret_key'));

            /**
             * Payment parameters:
             *   amount        Amount [currency, value]
             *   description   Description of the payment.
             *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
             *   webhookUrl    Webhook location, used to report when the payment changes state.
             *   metadata      Custom metadata that is stored with the payment.
             */
            $paymentCheckout = $mollie->payments->create([
                'amount' => [
                    'currency' => $store->currency,
                    'value' => $amount
                ],
                'description' => static::getDescription($checkout->id),
                'redirectUrl' => SiteHelper::hostUrl() . '/cart',
//                'webhookUrl' => SiteHelper::hostUrl() . '/mollie',
                'webhookUrl' => 'http://2718c64b.ngrok.io/mollie',
                'metadata' => [
                    'paymentId' => $checkout->id,
                ],
            ]);

            return static::returnRedirect($paymentCheckout->getCheckoutUrl());

        } catch (ApiException $e) {
            PaymentsLog::log($checkout->id, $e->getMessage() . $e->getTraceAsString());
            Yii::error($e->getMessage() . $e->getTraceAsString());
            return static::returnError();
        }
    }

    /**
     * Processing Mollie payment
     *
     * @inheritdoc
     */
    public function processing($store)
    {
        $this->log(json_encode($_POST, JSON_PRETTY_PRINT));

        $txnId = ArrayHelper::getValue($_POST, 'id');
        if (empty($txnId)) {
            return [
                'result' => 2,
                'content' => 'no data',
            ];
        }

        $paymentMethod = $this->getStorePayMethod($store, PaymentMethods::METHOD_MOLLIE);

        if (empty($paymentMethod)) {
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey(ArrayHelper::getValue($paymentMethodOptions, 'secret_key'));
            $payment = $mollie->payments->get($txnId);
            $paymentId = $payment->metadata->paymentId;
            $paymentStatus = strtolower(trim($payment->status));
            $profileId = $payment->profileId;
            $country = $payment->countryCode ?? null;

            if (empty($paymentId) || !($this->_checkout = Checkouts::findOne(['id' => $paymentId, 'method_id' => $paymentMethod->id]))) {
                return [
                    'result' => 2,
                    'content' => 'no invoice'
                ];
            }

            $this->_payment = Payments::findOne(['checkout_id' => $paymentId]);

            if (!$this->_payment) {
                $this->_payment = new Payments();
                $this->_payment->method = $this->_method;
                $this->_payment->checkout_id = $this->_checkout->id;
                $this->_payment->amount = $this->_checkout->price;
                $this->_payment->customer = $this->_checkout->customer;
                $this->_payment->currency = $this->_checkout->currency;
                $this->_payment->country = $country;
                $this->_payment->memo = $profileId . '; ' . $txnId;
            } elseif ($this->_payment->method != $this->_method) {
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad invoice'
                ];
            }

            // save Log to DB
            $this->_logPayment($payment);

            if ((float)$payment->amount->value != (float)$this->_payment->amount) {
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad amount',
                ];
            }

            if ($payment->amount->currency != $store->currency) {
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad currency',
                ];
            }

            $this->_payment->transaction_id = $txnId;
            $this->_payment->response_status = $paymentStatus;
            $this->_checkout->method_status = $paymentStatus;

            if (!$payment->isPaid() || $payment->hasRefunds() || $payment->hasChargebacks()) {

                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'other payment status',
                ];

            }

            $this->_payment->status = Payments::STATUS_AWAITING;

            return [
                'checkout_id' => $paymentId,
                'result' => 1,
                'transaction_id' => $txnId,
                'fee' => 0,
                'amount' => $this->_payment->amount,
                'payment_id' => $this->_payment->id,
                'content' => 'Ok',
            ];


        } catch (ApiException $e) {

            $this->log(json_encode($e->getMessage() . $e->getTraceAsString(), JSON_PRETTY_PRINT));
            Yii::error($e->getMessage() . $e->getTraceAsString());

            return [
                'result' => 2,
                'content' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param Payment $payment
     */
    private function _logPayment(Payment $payment)
    {
        if ($payment) {
            $response = ArrayHelper::toArray($payment, [
                'id',
                'mode',
                'settlementAmount',
                'amount',
                'amountRefunded',
                'amountRemaining',
                'amountRemaining',
                'details',
                'description',
                'method',
                'status',
                'createdAt',
                'paidAt',
                'canceledAt',
                'expiresAt',
                'failedAt',
                'profileId',
                'sequenceType',
                'redirectUrl',
                'webhookUrl',
                'mandateId',
                'subscriptionId',
                'orderId',
                'locale',
                'isCancelable',
            ]);
        } else {
            $response = $_POST;
        }

        PaymentsLog::log($this->_checkout->id, $response);
    }

}