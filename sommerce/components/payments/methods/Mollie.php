<?php

namespace sommerce\components\payments\methods;

use common\models\stores\PaymentMethods;
use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\store\Checkouts;
use yii\base\Exception;
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

//INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`) VALUES (NULL, 'mollie', '[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\"]', 'Mollie', 'Mollie', 'mollie', '17', '{\"secret_key\":\"\"}', '1');

    /**
     * @var string - url action
     */
    public $action = '';

    protected $_paymentPoint = '';

    protected $_apiKey = '';

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
            $mollie->setApiKey(ArrayHelper::getValue($paymentMethodOptions, 'api_key'));
//            $amount = number_format((float)$checkout->price, 2, '.', '');

            $paymentCheckout = $mollie->payments->create([
                'amount' => [
                    'currency' => $store->currency,
                    'value' => $amount
                ],
                'description' => static::getDescription($checkout->id),
                'redirectUrl' => SiteHelper::hostUrl() . '/cart',
                'webhookUrl' => SiteHelper::hostUrl() . '/mollie/' . $checkout->id,
                'metadata' => [
                    'paymentId' => $checkout->id,
                ],
            ]);

            return static::returnRedirect($paymentCheckout->getCheckoutUrl());

        } catch (ApiException $e) {
//            $this->dbLog($payment, $e->getMessage() . $e->getTraceAsString());
            PaymentsLog::log($this->_checkout->id, $e->getMessage() . $e->getTraceAsString());

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

        $paymentMethodOptions = $paymentMethod->getDetails();

        if (empty($_POST['id'])) {
            return [
                'result' => 2,
                'content' => 'bad data',
//                'reason' => 'bad data'
            ];
        }


        try {
            $mollie = new MollieApiClient();
            $mollie->setApiKey(ArrayHelper::getValue($paymentMethodOptions, 'api_key'));
            $payment = $mollie->payments->get($_POST['id']);
            $paymentId = $payment->metadata->paymentId;
//            $paymentId = $this->_checkout->id;

            if ($paymentId != $this->_checkout->id) {
                return [
                    'result' => 2,
                    'content' => 'bad checkout',
                ];
            }

            if (!($this->_payment = Payments::findOne([
                'checkout_id' => $this->_checkout->id,
            ]))) {
                $this->_payment = new Payments();
                $this->_payment->method = $this->_method;
                $this->_payment->checkout_id = $this->_checkout->id;
                $this->_payment->amount = $this->_checkout->price;
                $this->_payment->customer = $this->_checkout->customer;
                $this->_payment->currency = $this->_checkout->currency;
            } else if ($this->_payment->method != $this->_method) {
                // no invoice
                return [
                    'checkout_id' => $paymentId,
                    'result' => 2,
                    'content' => 'bad invoice payment'
                ];
            }

            $this->_logPayment($payment);

            $this->_payment->response_status = $payment->status;
            $this->_payment->transaction_id = $payment->id;

            if (!$payment->isPaid() || $payment->hasRefunds() || $payment->hasChargebacks()) {
                return [
                    'result' => 2,
                    'content' => 'other payment status',
//                    'reason' => 'other payment status'
                ];
            }

            if ((float)$payment->amount->value != (float)$this->_payment->amount) {
                // bad amount
                return [
                    'result' => 2,
                    'content' => 'bad amount',
//                    'reason' => 'bad amount'
                ];
            }

            if ($payment->amount->currency != $store->currency) {
                // bad amount
                return [
                    'result' => 2,
                    'content' => 'bad currency',
//                    'reason' => 'bad currency'
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
//            $this->fileLog($e->getMessage() . $e->getTraceAsString());

//            PaymentsLog::log($this->_checkout->id, $_POST);

            $this->log(json_encode($e->getMessage() . $e->getTraceAsString(), JSON_PRETTY_PRINT));

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

        PaymentsLog::log($response, $_POST);

        $this->log(json_encode($response, JSON_PRETTY_PRINT));
    }
}