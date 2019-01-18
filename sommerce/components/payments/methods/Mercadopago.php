<?php

namespace sommerce\components\payments\methods;

use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use Yii;
use MP;
use MercadoPagoException;
use yii\helpers\ArrayHelper;
use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\store\Checkouts;
use common\models\stores\StorePaymentMethods;

/**
 * Class Mercadopago
 * @package app\components\payments\methods
 */
class Mercadopago extends BasePayment
{

    /**
     * @var string - url action
     */
    public $action = null;

    public $redirectProcessing = true;

    /**
     * Checkout
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
     * @return array|mixed
     * @throws MercadoPagoException
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        $clientId = ArrayHelper::getValue($paymentMethodOptions, 'client_id');
        $clientSecret = ArrayHelper::getValue($paymentMethodOptions, 'secret');

        if (!$clientId || !$clientSecret) {
            return static::returnError();
        }

        /**
         * @var $client MP
         */
        $client = new MP($clientId, $clientSecret);

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $client->sandbox_mode(true);
        }

        $amount = $checkout->price;

        $clientData = [
            "external_reference" => $checkout->id,
            'customer' => [
                "email" => $email,
            ],
            'items' => [
                [
                    "id" => $checkout->id,
                    "title" => static::getDescription($email),
                    "description" => static::getDescription($email),
                    "quantity" => 1,
                    "currency_id" => $store->currency,
                    "unit_price" => (float)$amount,
                ]
            ],
            'back_urls' => [
                'success' => SiteHelper::hostUrl($store->ssl) . '/mercadopago',
                'failure' => SiteHelper::hostUrl($store->ssl) . '/addfunds',
                'pending' => SiteHelper::hostUrl($store->ssl) . '/addfunds',
            ],
            "notification_url" => SiteHelper::hostUrl($store->ssl) . '/mercadopago',
        ];

        $response = null;

        try {
            $response = $client->create_preference($clientData);
            $responseDetails = ArrayHelper::getValue($response, 'response', []);

            if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
                $link = ArrayHelper::getValue($responseDetails, 'sandbox_init_point');
            } else {
                $link = ArrayHelper::getValue($responseDetails, 'init_point');
            }

        } catch (MercadoPagoException $e) {
            Yii::error($e->getTraceAsString());
        }

        if (empty($link)) {
            $this->log($response);

            return static::returnError();
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($checkout->id, $response);

        return self::returnRedirect($link);
    }

    /**
     * @param Stores $store
     * @return array|mixed
     * @throws MercadoPagoException
     */
    public function processing($store)
    {
        $id = ArrayHelper::getValue($_GET, 'id');
        $topic = ArrayHelper::getValue($_GET, 'topic');

        if (!$id || !$topic) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        if ('payment' != strtolower((string)$topic)) {
            return [
                'result' => 2,
                'content' => 'bad data'
            ];
        }

        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_MERCADOPAGO);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();

        $clientId = ArrayHelper::getValue($paymentMethodOptions, 'client_id');
        $clientSecret = ArrayHelper::getValue($paymentMethodOptions, 'secret');

        if (!$clientId || !$clientSecret) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        /**
         * @var $client MP
         */
        $client = new MP($clientId, $clientSecret);

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $client->sandbox_mode(true);
        }

        $response = null;

        try {
            $response = $client->get_payment_info($_GET["id"]);
            $paymentInfoResponse = ArrayHelper::getValue($response, 'response', []);
        } catch (MercadoPagoException $e) {
            Yii::error($e->getTraceAsString());
        }

        $this->log(is_string($response) ? $response : json_encode($response, JSON_PRETTY_PRINT));

        if (empty($paymentInfoResponse)) {
            return [
                'result' => 2,
                'content' => 'bad payment'
            ];
        }

        $checkoutId = $paymentInfoResponse['collection']['external_reference'];
        $status = $paymentInfoResponse['collection']['status'];
        $amount = $paymentInfoResponse['collection']['transaction_amount'];
        $currency = $paymentInfoResponse['collection']['currency_id'];

        if (empty($checkoutId)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $checkoutId,
                'method_id' => $paymentMethod->method_id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no invoice
            return [
                'checkout_id' => $checkoutId,
                'result' => 2,
                'content' => 'no invoice'
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
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $_POST);
        $this->log(json_encode($_POST, JSON_PRETTY_PRINT));

        if (strtolower($currency) != strtolower($store->currency)) {
            return [
                'result' => 2,
                'content' => 'Bad currency'
            ];
        }

        $paymentAmount = $this->_payment->amount; // сумму оплаты payments.amount

        if ($paymentAmount != $amount) {
            return [
                'result' => 2,
                'content' => 'Bad amount'
            ];
        }

        $this->_payment->transaction_id = $id;
        $this->_payment->response_status = $status;
        $this->_payment->status = Payments::STATUS_AWAITING;

        if ($status != 'approved') {
            return [
                'result' => 2,
                'content' => 'No final status'
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $id,
            'amount' => $this->_payment->amount,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}