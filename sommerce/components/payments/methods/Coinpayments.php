<?php

namespace sommerce\components\payments\methods;

use Yii;
use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use yii\helpers\ArrayHelper;
use common\models\stores\StorePaymentMethods;

class Coinpayments extends BasePayment
{

    public $redirectProcessing = false;

    /**
     * Form url for form redirect after checkout
     * @var string
     */
    public $action = 'https://www.coinpayments.net/index.php';

    /**
     * Form method for form redirect after checkout
     * @var string
     */
    public $method = 'POST';

    /**
     * Is shown processing & checkout errors messages
     * @var bool
     */
    public $showErrors = false;

    /**
     * Incomming IPN messages url
     * @var string
     */
    public static $ipnUrl = '/coinpayments';

    /**
     * CoinPayments IPN statuses
     * according to official documentations
     * https://www.coinpayments.net/merchant-tools-ipn
     */
    const PAYMENT_STATUS_PAYPAL_REFUND_REVERSAL = -2;
    const PAYMENT_STATUS_CANCELLED_TIMEOUT = -1;
    const PAYMENT_STATUS_WAITING_FUNDS = 0;
    const PAYMENT_STATUS_CONFIRMED_COIN_RECEPTION = 1;
    const PAYMENT_STATUS_QUEUED_NIGHTLY_PAYOUT = 2;
    const PAYMENT_STATUS_PAYPAL_PENDING = 3;
    const PAYMENT_STATUS_COMPLETED = 100;

    private static $_allowedIPNStatuses = [
        self::PAYMENT_STATUS_PAYPAL_REFUND_REVERSAL,
        self:: PAYMENT_STATUS_CANCELLED_TIMEOUT,
        self::PAYMENT_STATUS_WAITING_FUNDS,
        self::PAYMENT_STATUS_CONFIRMED_COIN_RECEPTION,
        self::PAYMENT_STATUS_QUEUED_NIGHTLY_PAYOUT,
        self::PAYMENT_STATUS_PAYPAL_PENDING,
        self::PAYMENT_STATUS_COMPLETED
    ];

    /**
     * Checkout routine
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        $merchantId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_id', null);
        $ipnSecret = ArrayHelper::getValue($paymentMethodOptions, 'ipn_secret', null);

        if (!isset($merchantId, $ipnSecret)) {
            return static::returnError();
        }

        $amount = number_format($checkout->price, 2, '.', '');

        return static::returnForm($this->getFrom(), [
            'cmd' => '_pay',
            'reset' => '1',
            'want_shipping' => '0',
            'merchant' => $merchantId,
            'currency' => $store->currency,
            'amountf' => $amount,
            'item_desc' => static::getDescription($checkout->id),
            'item_name' => static::getDescription($checkout->id),
            'success_url' => SiteHelper::hostUrl(),
            'cancel_url' => SiteHelper::hostUrl(),
            'ipn_url' => SiteHelper::hostUrl() . static::$ipnUrl,
            'invoice' => $checkout->id,
            'email' => $email,
            'allow_extra' => '1',
        ]);
    }

    /**
     * Processing CoinPayments IPN requests
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $this->log(json_encode($_POST, JSON_PRETTY_PRINT));

        $ipnData = [
            'hmac_signature' => ArrayHelper::getValue($_SERVER, 'HTTP_HMAC'),
            'transaction_id' => ArrayHelper::getValue($_POST, 'txn_id'),
            'ipn_mode' => ArrayHelper::getValue($_POST, 'ipn_mode'),
            'merchant_id' => ArrayHelper::getValue($_POST, 'merchant'),
            'ipn_status' => ArrayHelper::getValue($_POST, 'status'),
            'payment_currency' => ArrayHelper::getValue($_POST, 'currency1'),
            'payment_amount' => ArrayHelper::getValue($_POST, 'amount1'),
            'payment_email' => ArrayHelper::getValue($_POST, 'email'),
            'sommerce_checkout_id' => ArrayHelper::getValue($_POST, 'invoice'),
        ];

        if (in_array('', $ipnData)) {
            return [
                'result' => 2,
                'content' => "Missing required CoinPayments IPN params!"
            ];
        }

        if (!in_array($ipnData['ipn_status'], static::$_allowedIPNStatuses)) {
            return [
                'result' => 2,
                'content' => "Unknown CoinPayments IPN status! Status=" . $ipnData['ipn_status']
            ];
        }

        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_COINPAYMENTS,
            'store_id' => $store->id,
            'visibility' => StorePaymentMethods::VISIBILITY_ENABLED
        ]);

        if (empty($paymentMethod)) {
            return [
                'result' => 2,
                'content' => "Bad payment method!"
            ];
        }

        $methodDetails = $paymentMethod->getOptions();
        $methodMerchantId = ArrayHelper::getValue($methodDetails, 'merchant_id');
        $methodIPNSecret = ArrayHelper::getValue($methodDetails, 'ipn_secret');

        if (!isset($methodMerchantId, $methodIPNSecret)) {
            return [
                'result' => 2,
                'content' => "Invalid CoinPayments settings!"
            ];
        }

        if ($methodMerchantId !== $ipnData['merchant_id']) {
            return [
                'result' => 2,
                'content' => "Unexpected merchant ID!" . "Expected:" . $methodMerchantId . ", given: " . $ipnData['merchant_id']
            ];
        }

        // Validate CoinPayments message
        $requestRawBody = file_get_contents('php://input');
        if (empty($requestRawBody)) {
            return [
                'result' => 2,
                'content' => "Error reading raw request data!"
            ];
        }

        $hmac = hash_hmac("sha512", $requestRawBody, trim($methodIPNSecret));
        if (!hash_equals($hmac, $ipnData['hmac_signature'])) {
            return [
                'result' => 2,
                'content' => "HMAC signature does not match!" . "Expected: " . $hmac . ", given: " . $ipnData['hmac_signature'],
            ];
        }

        // Check checkout
        if (empty($ipnData['sommerce_checkout_id'])
            || !($this->_checkout = Checkouts::findOne([
                'id' => $ipnData['sommerce_checkout_id'],
                'method_id' => $paymentMethod->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no checkout
            return [
                'result' => 2,
                'content' => "Checkout #" . $ipnData['sommerce_checkout_id'] . " does not exist or already paid!"
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
                'checkout_id' => $ipnData['sommerce_checkout_id'],
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        // Logging PS checkout request
        PaymentsLog::log($this->_checkout->id, $_POST);

        // Check invoice currency. Binary safe case-insensitive.
        if (strcasecmp($ipnData['payment_currency'], $this->_checkout->currency) !== 0) {
            return [
                'result' => 2,
                'content' => "Invalid checkout currency code verification result! Expected:" . $this->_checkout->currency . ", given: " . $ipnData['payment_currency'],
            ];
        }

        // Check payment amount
        $paymentAmount = number_format($ipnData['payment_amount'], 2, '.', '');
        $checkoutAmount = number_format($this->_checkout->price, 2, '.', '');
        if ($paymentAmount != $checkoutAmount) {
            return [
                'result' => 2,
                'content' => "Invalid amount verification result! Expected: $checkoutAmount, Given: $paymentAmount"
            ];
        }

        $this->_checkout->method_status = $ipnData['ipn_status'];

        $this->_payment->transaction_id = $ipnData['transaction_id'];
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $ipnData['ipn_status'];
        $this->_payment->email = $ipnData['payment_email'];

        if (!in_array($ipnData['ipn_status'], [self::PAYMENT_STATUS_COMPLETED, self::PAYMENT_STATUS_QUEUED_NIGHTLY_PAYOUT])) {
            return [
                'result' => 2,
                'content' => "The payment is not yet completed. Current status: " . $ipnData['ipn_status']
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $ipnData['transaction_id'],
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}