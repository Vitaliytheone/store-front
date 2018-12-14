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

    /**
     * @var string - url action
     */
    public $action = '';

    protected $_paymentPoint = '';

    /**
     * @var string store PaymentID
     */
    protected $_transactionId;

    public $redirectProcessing = false;

    public $showErrors = true; //TODO del

    /**
     * @inheritdoc
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getDetails();

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
                'redirectUrl' => SiteHelper::hostUrl() . '/mollie/' . $checkout->id,
                'webhookUrl' => 'http://93c394c3.ngrok.io' . '/mollie/' . $checkout->id,
                'metadata' => [
                    'paymentId' => $checkout->id,
                ],
            ]);

//            $this->_transactionId = $paymentCheckout->id;
//            Yii::debug($this->_transactionId);
//            Yii::debug($paymentCheckout->getCheckoutUrl());

            return static::returnRedirect($paymentCheckout->getCheckoutUrl());

        } catch (ApiException $e) {
            PaymentsLog::log($checkout->id, $e->getMessage() . $e->getTraceAsString());
            Yii::error($e->getMessage() . $e->getTraceAsString());
            return static::returnError();
        }
    }

    /**
     * @inheritdoc
     */
    public function processing($store)
    {
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_MOLLIE,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $txnId = ArrayHelper::getValue($_POST, 'id');
        $id = ArrayHelper::getValue($_GET, 'id');

        Yii::debug($id);
        Yii::debug($txnId);

        $this->_payment = Payments::findOne(['checkout_id' => $id]);

        // after redirect POST is empty, show Payment Awaiting
        if (empty($_POST) && empty($this->_payment)) {
            return [
                'checkout_id' => $id,
                'result' => 2,
            ];
        }
        if (!empty($this->_payment) && $this->_payment->status == Payments::STATUS_COMPLETED) {
            return [
                'checkout_id' => $id,
                'result' => 1,
            ];
        }

        $paymentMethodOptions = $paymentMethod->getDetails();

        if (empty($id) || !($this->_checkout = Checkouts::findOne([
                'id' => $id,
                'method_id' => $paymentMethod->id
            ]))) {
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'no invoice'
            ];
        }

        if (empty($txnId)) {
            return [
                'result' => 2,
                'content' => 'no data',
            ];
        }

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey(ArrayHelper::getValue($paymentMethodOptions, 'secret_key'));
            $payment = $mollie->payments->get($txnId);
            $paymentId = $payment->metadata->paymentId;
            $paymentStatus = strtolower(trim($payment->status));
            $profileId = $payment->profileId;
            $country = $payment->countryCode ?? null; // FIXME не видит кантри код
//            $paymentId = $this->_checkout->id;

            if ($paymentId != $this->_checkout->id) {
                return [
                    'result' => 2,
                    'content' => 'bad checkout',
                ];
            }

            Yii::debug($payment);

            if (!$this->_payment) {
                $this->_payment = new Payments();
                $this->_payment->method = $this->_method;
                $this->_payment->checkout_id = $this->_checkout->id;
                $this->_payment->amount = $this->_checkout->price;
                $this->_payment->customer = $this->_checkout->customer;
                $this->_payment->currency = $this->_checkout->currency;
                $this->_payment->country = $country;
            } elseif ($this->_payment->method != $this->_method) {
                // no invoice
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad invoice'
                ];
            }

            $this->_logPayment($payment);

            $this->_checkout->method_status = $paymentStatus;

            $this->_payment->transaction_id = $txnId;
            $this->_payment->status = Payments::STATUS_AWAITING;
            $this->_payment->response_status = $paymentStatus;
//            $this->_payment->email = $payerEmail;
            $this->_payment->memo = $profileId . '; ' . $txnId;


            if ((float)$payment->amount->value != (float)$this->_payment->amount) {
                // bad amount
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad amount',
                ];
            }

            if ($payment->amount->currency != $store->currency) {
                // bad amount
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad currency',
                ];
            }

            if ($payment->hasRefunds() || $payment->hasChargebacks()) {
                $this->log(json_encode(['status' => 'Payment change status. Refund or Chargebacks receive'], JSON_PRETTY_PRINT));
                $this->_payment->status = Payments::STATUS_REFUNDED;
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'Refund or chargebacks status receive',
                ];
            }

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
                $this->_payment->status = Payments::STATUS_COMPLETED;
                Yii::debug('Paid');

                // if payments complete create suborder and clear cart
                static::success($this->_payment, [
                    'result' => 1,
                    'transaction_id' => $txnId,
                    'amount' => $this->_checkout->price,
                    'checkout_id' => $this->_checkout->id,
                ], $store);

            } elseif ($payment->isOpen() || $payment->isPending()) {

                Yii::debug('Awaiting');
                return [
                    'checkout_id' => $id,
                    'result' => 2,
                    'content' => 'payment awaiting',
                ];

            } elseif ($payment->isFailed() || $payment->isExpired() || $payment->isCanceled()) {
                $this->_payment->status = Payments::STATUS_FAILED;
                Yii::debug('Failed');

                return [
                    'checkout_id' => $id,
                    'result' => 2,
                    'content' => 'payment failed',
                ];

            }


            $this->_payment->status = Payments::STATUS_AWAITING;

            return [
                'result' => 1,
                'transaction_id' => $payment->id,
                'fee' => 0,
                'amount' => $this->_payment->amount,
                'payment_id' => $this->_payment->id,
                'content' => 'Ok'
            ];

        } catch (ApiException $e) {

            $this->log(json_encode($e->getMessage() . $e->getTraceAsString(), JSON_PRETTY_PRINT));
            Yii::error($e->getMessage() . $e->getTraceAsString());

            return [
                'result' => 2,
                'content' => $e->getMessage(),
//                'reason' => $e->getMessage()
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