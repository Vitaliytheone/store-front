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

    public $redirectProcessing = true;

    /**
     * Create checkout and redirect to Mollie pay site
     *
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
                'redirectUrl' => SiteHelper::hostUrl() . '/' . $this->_method . '/' . $checkout->id,
                'webhookUrl' => SiteHelper::hostUrl() . '/' . $this->_method . '/' . $checkout->id,
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
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_MOLLIE,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        if (empty($paymentMethod)) {
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $txnId = ArrayHelper::getValue($_POST, 'id');
        $id = ArrayHelper::getValue($_GET, 'id');

        // after redirect POST is empty, show Payment Awaiting
        if (empty($_POST)) {
            return [
                'checkout_id' => $id,
                'result' => 2,
            ];
        }

        if (empty($txnId)) {
            return [
                'result' => 2,
                'content' => 'no data',
            ];
        }

        $paymentMethodOptions = $paymentMethod->getDetails();

        if (empty($id) || !($this->_checkout = Checkouts::findOne(['id' => $id, 'method_id' => $paymentMethod->id]))) {
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'no invoice'
            ];
        }

        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey(ArrayHelper::getValue($paymentMethodOptions, 'secret_key'));
            $payment = $mollie->payments->get($txnId);
            $paymentId = $payment->metadata->paymentId;
            $paymentStatus = strtolower(trim($payment->status));
            $profileId = $payment->profileId;
            $country = $payment->countryCode ?? null;

            if ($paymentId != $this->_checkout->id) {
                return [
                    'result' => 2,
                    'content' => 'bad checkout',
                ];
            }

            $this->_payment = Payments::findOne(['checkout_id' => $id]);

            if (!$this->_payment) {
                $this->_payment = new Payments();
                $this->_payment->method = $this->_method;
                $this->_payment->checkout_id = $this->_checkout->id;
                $this->_payment->amount = $this->_checkout->price;
                $this->_payment->customer = $this->_checkout->customer;
                $this->_payment->currency = $this->_checkout->currency;
                $this->_payment->country = $country;
                $this->_payment->transaction_id = $txnId;
                $this->_payment->status = Payments::STATUS_AWAITING;
                $this->_payment->response_status = $paymentStatus;
                $this->_payment->memo = $profileId . '; ' . $txnId;
            } elseif ($this->_payment->method != $this->_method) {
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad invoice'
                ];
            }

            $this->_logPayment($payment);

            $this->_checkout->method_status = $paymentStatus;


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


            if ($payment->hasRefunds() || $payment->hasChargebacks()) {
                $this->_payment->status = Payments::STATUS_REFUNDED;
                $this->log(json_encode(['status' => 'Payment change status. Refund or chargeback receive'], JSON_PRETTY_PRINT));

                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'payment refund or chargeback',
                ];
            }

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
                $this->_payment->status = Payments::STATUS_COMPLETED;

                // if payments complete create suborder and clear cart
                static::success($this->_payment, [
                    'result' => 1,
                    'transaction_id' => $txnId,
                    'amount' => $this->_checkout->price,
                    'checkout_id' => $this->_checkout->id,
                ], $store);

                return [
                    'checkout_id' => $id,
                    'result' => 1,
                    'content' => 'payment paid',
                ];

            } elseif ($payment->isOpen() || $payment->isPending()) {

                return [
                    'checkout_id' => $id,
                    'result' => 2,
                    'content' => 'payment awaiting',
                ];

            } elseif ($payment->isFailed() || $payment->isExpired() || $payment->isCanceled()) {
                $this->_payment->status = Payments::STATUS_FAILED;

                return [
                    'checkout_id' => $id,
                    'result' => 2,
                    'content' => 'payment failed',
                ];

            }


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

    /**
     * Use own method instead parent, because if there was no POST request,
     * the page "Payment Failed" is displayed.
     *
     * @inheritdoc
     */
    public static function getPaymentResult($checkoutId): array
    {
        $paymentsResult = [
            'id' => $checkoutId,
        ];

        $checkout = Checkouts::findOne(['id' => $checkoutId]);
        $payment = Payments::findOne(['checkout_id' => $checkoutId, 'method' => PaymentMethods::METHOD_MOLLIE]);

        if (empty($checkout)) {
            $paymentsResult['failed'] = true;
        } elseif (!$payment) {
            $paymentsResult['awaiting'] = true;
        } else {
            $paymentsResult['failed'] = in_array($payment->status, [Payments::STATUS_FAILED]);
            $paymentsResult['awaiting'] = in_array($payment->status, [Payments::STATUS_AWAITING]);
            $paymentsResult['completed'] = in_array($payment->status, [Payments::STATUS_COMPLETED]);
        }

        return $paymentsResult;
    }

}