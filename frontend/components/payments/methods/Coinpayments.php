<?php

namespace frontend\components\payments\methods;

use Yii;
use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use frontend\components\payments\BasePayment;
use common\helpers\SiteHelper;
use yii\helpers\ArrayHelper;

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
    public $showErrors = true;

    /**
     * Incomming IPN messages url
     * @var string
     */
    public static $ipnUrl = '/coinpayments';

    /**
     * Coin Payments IPN statuses
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
     * @param PaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getDetails();

        $merchantId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_id', null);
        $ipnSecret = ArrayHelper::getValue($paymentMethodOptions, 'ipn_secret', null);

        if (!isset($merchantId, $ipnSecret)) {
            return static::returnError();
        }

        $amount = number_format($checkout->price, 2, '.', '');

        return static::returnForm($this->getFrom(), [
            'cmd' => '_pay_simple',
            'reset' => 1,
            'amountf' => $amount,
            'currency' => $store->currency,
            'merchant' => $merchantId,
            'invoice' => $checkout->id,
            'item_name' => static::getDescription($email),
            'success_url' => SiteHelper::hostUrl(),
            'cancel_url' => SiteHelper::hostUrl(),
            'ipn_url' => SiteHelper::hostUrl() . static::$ipnUrl,
            'email' => $email,
        ]);
    }

    public function processing($store)
    {
        $request = Yii::$app->request;
        $requestRawBody = $request->getRawBody();
        $requestParams = $request->post();
        $requestHeaders = $request->getHeaders();

        error_log(print_r($requestParams, 1), 0);

        $this->log(json_encode($requestParams, JSON_PRETTY_PRINT));

        $ipnTransactionId = ArrayHelper::getValue($requestParams, 'txn_id');
        $ipnMode = ArrayHelper::getValue($requestParams, 'ipn_mode');
        $ipnHmacSignature = ArrayHelper::getValue($requestHeaders, 'HTTP_HMAC');
        $ipnMerchant = ArrayHelper::getValue($requestParams, 'merchant');
        $ipnStatus = ArrayHelper::getValue($requestParams, 'status');
        $ipnCurrency = ArrayHelper::getValue($requestParams, 'currency1');
        $ipnAmount = ArrayHelper::getValue($requestParams, 'amount1');
        $ipnEmail = ArrayHelper::getValue($requestParams, 'email');
        $ipnVendorCheckoutId = ArrayHelper::getValue($requestParams, 'invoice');


        if (!isset($ipnTransactionId, $ipnMode, $ipnHmacSignature, $ipnMerchant, $ipnStatus, $ipnCurrency, $ipnAmount, $ipnEmail, $ipnVendorCheckoutId)) {
            return [
                'result' => 2,
                'content' => "Invalid Coin Payments IPN params!"
            ];
        }

        if (!in_array($ipnMode, static::$_allowedIPNStatuses)) {
            return [
                'result' => 2,
                'content' => "Unknown Coin Payments IPN status!"
            ];
        }

        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_COINPAYMENTS,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        if (empty($paymentMethod)) {
            return [
                'result' => 2,
                'content' => "Bad payment method!"
            ];
        }

        $paymentDetails = $paymentMethod->getDetails();
        $paymentMerchantId = ArrayHelper::getValue($paymentDetails, 'merchant_id');
        $paymentIPNSecret = ArrayHelper::getValue($paymentDetails, 'ipn_secret');

        if (!isset($paymentMerchantId, $paymentIPNSecret)) {
            return [
                'result' => 2,
                'content' => "Invalid Coin Payments settings!"
            ];
        }

        $hmac = hash_hmac("sha512", $requestRawBody, trim($paymentIPNSecret));

        if (empty($rawRequest)) {
            return [
                'result' => 2,
                'content' => "Error reading raw request data!"
            ];
        }

        // Validate Coin Payments message
        if (!hash_equals($hmac, $ipnHmacSignature)) {
            return [
                'result' => 2,
                'content' => "HMAC signature does not match!"
            ];
        }

        // Check checkout
        if (empty($ipnVendorCheckoutId)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $ipnVendorCheckoutId,
                'method_id' => $paymentMethod->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no checkout
            return [
                'result' => 3,
                'content' => "Checkout #$ipnVendorCheckoutId does not exist or already paid!"
            ];
        }

        // Logging PS checkout request
        PaymentsLog::log($this->_checkout->id, $requestParams);

        // Check invoice currency. Binary safe case-insensitive.
        if (strcasecmp($ipnCurrency, $store->currency) !== 0) {
            return [
                'result' => 2,
                'content' => "Invalid checkout currency code verification result! Expected: $store->currency, Current: $ipnCurrency"
            ];
        }

        // Check payment amount
        $ipnAmount = number_format($ipnAmount, 2, '.', '');
        $checkoutAmount = number_format($this->_checkout->price, 2, '.', '');
        if ($ipnAmount != $ipnAmount) {
            return [
                'result' => 2,
                'content' => "Invalid amount verification result! Expected: $checkoutAmount, Given: $ipnAmount"
            ];
        }

        $this->_checkout->method_status = $ipnStatus;

        $this->_payment->transaction_id = $ipnTransactionId;
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $ipnStatus;
        $this->_payment->email = $ipnEmail;

        if (!in_array($ipnStatus, [self::PAYMENT_STATUS_COMPLETED, self::PAYMENT_STATUS_QUEUED_NIGHTLY_PAYOUT])) {
            return [
                'result' => 2,
                'content' => "The payment is not yet completed. Current status: $ipnStatus."
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $ipnTransactionId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}