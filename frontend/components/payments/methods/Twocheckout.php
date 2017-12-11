<?php
namespace frontend\components\payments\methods;

use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use Yii;
use frontend\components\payments\BasePayment;
use common\helpers\SiteHelper;
use yii\helpers\ArrayHelper;

/**
 * Class Twocheckout
 * @package app\components\payments\methods
 */
class Twocheckout extends BasePayment {

    public $action;
    public $method = 'GET';
    public $redirectProcessing = false;
    public $showErrors = true;

    const TEST_MODE_ON = 1;
    const MODE_TEST_OFF = 0;

    const HOSTED_URL_SANDBOX = 'https://sandbox.2checkout.com/checkout/purchase';
    const HOSTED_URL_PRODUCTION = 'https://www.2checkout.com/checkout/purchase';

    // Notification message types
    const MESSAGE_TYPE_ORDER_CREATED = 'ORDER_CREATED';
    const MESSAGE_TYPE_FRAUD_STATUS_CHANGED = 'FRAUD_STATUS_CHANGED';
    const MESSAGE_TYPE_SHIP_STATUS_CHANGED = 'SHIP_STATUS_CHANGED';
    const MESSAGE_TYPE_INVOICE_STATUS_CHANGED = 'INVOICE_STATUS_CHANGED';
    const MESSAGE_TYPE_REFUND_ISSUED = 'REFUND_ISSUED';
    const MESSAGE_TYPE_RECURRING_INSTALLMENT_SUCCESS = 'RECURRING_INSTALLMENT_SUCCESS';
    const MESSAGE_TYPE_RECURRING_INSTALLMENT_FAILED = 'RECURRING_INSTALLMENT_FAILED';
    const MESSAGE_TYPE_RECURRING_COMPLETE = 'RECURRING_COMPLETE';
    const MESSAGE_TYPE_RECURRING_RESTARTED = 'RECURRING_RESTARTED';

    // Fraud review statuses
    const FRAUD_STATUS_PASS = 'pass';
    const FRAUD_STATUS_WAIT = 'wait';
    const FRAUD_STATUS_FAIL = 'fail';

    // Allowed values of fraud_status message field
    private static $_allowedFraudStatuses = [
        self::FRAUD_STATUS_PASS,
        self::FRAUD_STATUS_WAIT,
        self::FRAUD_STATUS_FAIL,
    ];

    // Allowed 2Checkout message types
    private static $_allowedMessageTypes = [
        self::MESSAGE_TYPE_ORDER_CREATED,
        self::MESSAGE_TYPE_FRAUD_STATUS_CHANGED,
    ];

    public function __construct(array $config = [])
    {
        return parent::__construct($config);
    }

    /**
     * Checkout
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param PaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getDetails();
        $mode = (int)ArrayHelper::getValue($paymentMethodOptions, 'test_mode', null);
        $accountNumber = ArrayHelper::getValue($paymentMethodOptions, 'account_number', null);
        $secretWord = ArrayHelper::getValue($paymentMethodOptions, 'secret_word', null);

        if (!isset($mode, $accountNumber, $secretWord)) {
            return static::returnError();
        }

        $amount = number_format($checkout->price, 2, '.', '');
        $receiptLinkUrl = SiteHelper::hostUrl() . '/twocheckout';

        /**
         * `sid`    Your 2Checkout account number.
         * `mode`   Defines the parameter set. Should always be passed as ‘2CO’.
         * `li_0_type`  The type of line item that is being passed in. (Always Lower Case, ‘product’, ‘shipping’, ‘tax’ or ‘coupon’, defaults to ‘product’)
         * `li_0_name`  Name of the item passed in with the corresponding li_#_type. (128 characters max, cannot use ‘<' or '>’, defaults to capitalized version of ‘type’.)
         * `li_0_quantity`  Quantity of the item passed in with the corresponding li_#_type. (0-999, defaults to 1 if not passed in.)
         * `li_0_price`     Price of the line item. Format: 0.00-99999999.99, defaults to 0 if a value isn’t passed in, no negatives (use positive values for coupons), leading 0 & decimal are optional. Important note: If the li_#_price parameter isn’t used to pass in line item pricing, the pricing for the corresponding item will default to 0.00.
         * `li_0_tangible`  Specifies if the corresponding li_#_type is a tangible or intangible. ( Must be Upper Case, ‘Y’ or ‘N’, if li_#_type is ‘shipping’ forced to ‘Y’.)
         * `currency_code`  AFN, ALL, DZD, ARS, AUD, AZN, BSD, BDT, BBD, BZD, BMD, BOB, BWP, BRL, GBP, BND, BGN, CAD, CLP, CNY, COP, CRC, HRK, CZK, DKK, DOP, XCD, EGP, EUR, FJD, GTQ, HKD, HNL, HUF, INR, IDR, ILS, JMD, JPY, KZT, KES, LAK, MMK, LBP, LRD, MOP, MYR, MVR, MRO, MUR, MXN, MAD, NPR, TWD, NZD, NIO, NOK, PKR, PGK, PEN, PHP, PLN, QAR, RON, RUB, WST, SAR, SCR, SGD, SBD, ZAR, KRW, LKR, SEK, CHF, SYP, THB, TOP, TTD, TRY, UAH, AED, USD, VUV, VND, XOF, YER. Use to specify the currency for the sale.
         * `purchase_step`  Sets the purchase step that the buyer will land on when being directed to the checkout page. Possible values are ‘review-cart’, ‘shipping-information’, ‘shipping-method’, ‘billing-information’ and ‘payment-method’. Please Note: To skip a purchase step, all required fields must be pre-populated with the parameters that are passed in with the sale. If a required field is not pre-populated, the buyer will revert back to the step that needs to be completed.
         * `merchant_order_id`  Specify your order number with this parameter. It will also be included in the confirmation emails to yourself and the customer. (50 characters max)
         * `x_receipt_link_url` Used to specify an approved URL on-the-fly, but is limited to the same domain that is used for your 2Checkout account, otherwise it will fail. This parameter will over-ride any URL set on the Site Management page. (no limit)
         */
        $queryParams = http_build_query([
            'sid' => $accountNumber,
            'mode' => '2CO',
            'li_0_type' => 'product',
            'li_0_name' => static::getDescription($email),
            'li_0_quantity' => 1,
            'li_0_price' => $amount,
            'li_0_tangible' => 'N',
            'currency_code' => $store->currency,
            'purchase_step' => 'review-cart',
            'merchant_order_id' => $checkout->id,
            'x_receipt_link_url' => $receiptLinkUrl,
        ]);

        $queryUrl = $mode === self::TEST_MODE_ON ? self::HOSTED_URL_SANDBOX : self::HOSTED_URL_PRODUCTION;

        return static::returnRedirect($queryUrl . '?' . $queryParams);
    }

   public function processing($store)
   {
       $request = Yii::$app->request;
       $requestParams = $request->post();

       error_log(print_r($requestParams, 1), 0);

       $messageType = ArrayHelper::getValue($requestParams, 'message_type', null);
       $messageFraudStatus = ArrayHelper::getValue($requestParams, 'fraud_status', null);
       $messageVendorOrderId = ArrayHelper::getValue($requestParams, 'vendor_order_id', null);
       $messageCurrency = ArrayHelper::getValue($requestParams, 'cust_currency', null);
       $messageAmount = ArrayHelper::getValue($requestParams, 'invoice_list_amount', null);
       $messageHash = ArrayHelper::getValue($requestParams, 'md5_hash', null);
       $messageSaleId = ArrayHelper::getValue($requestParams, 'sale_id', null);
       $messageVendorId = ArrayHelper::getValue($requestParams, 'vendor_id', null);
       $messageInvoiceId = ArrayHelper::getValue($requestParams, 'invoice_id', null);
       $messageCustomerEmail = ArrayHelper::getValue($requestParams, 'customer_email', null);


       $this->log(json_encode($requestParams, JSON_PRETTY_PRINT));

       if (!isset($messageType, $messageFraudStatus, $messageVendorOrderId, $messageCurrency, $messageAmount, $messageHash, $messageSaleId, $messageVendorId, $messageInvoiceId)) {
           return [
               'result' => 2,
               'content' => "Invalid messages params!"
           ];
       }

       if (!$messageType || !in_array($messageType, static::$_allowedMessageTypes)) {
           return [
               'result' => 2,
               'content' => "Unknown 2Checkout callback message type!"
           ];
       }

       if (!$messageFraudStatus || !in_array($messageFraudStatus, static::$_allowedFraudStatuses)) {
           return [
               'result' => 2,
               'content' => "Unknown 2Checkout fraud status!"
           ];
       }

       $paymentMethod = PaymentMethods::findOne([
           'method' => PaymentMethods::METHOD_2CHECKOUT,
           'store_id' => $store->id,
           'active' => PaymentMethods::ACTIVE_ENABLED
       ]);

       if (empty($paymentMethod)) {
           return [
               'result' => 2,
               'content' => 'Bad payment method'
           ];
       }

       $paymentDetails = $paymentMethod->getDetails();
       $secretWord = ArrayHelper::getValue($paymentDetails, 'secret_word', null);

       if (empty($secretWord)) {
           return [
               'result' => 2,
               'content' => 'Secret word does not exist.'
           ];
       }

       // Validate 2Checkout message
       $hashForMatch = strtoupper(md5($messageSaleId . $messageVendorId . $messageInvoiceId . $secretWord));
       if ($messageHash != $hashForMatch) {
           return [
               'result' => 2,
               'content' => 'Secret word does not exist.'
           ];
       }

       // Check checkout
       if (empty($messageVendorOrderId)
           || !($this->_checkout = Checkouts::findOne([
               'id' => $messageVendorOrderId,
               'method_id' => $paymentMethod->id
           ]))
           || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
           // no invoice
           return [
               'result' => 2,
               'content' => 'Invoice does not exist!'
           ];
       }

       // Logging PS checkout request
       PaymentsLog::log($messageVendorOrderId, $requestParams);

       // Check payment currency
       if (strcasecmp($messageCurrency, $store->currency) !== 0) {
           return [
               'result' => 2,
               'content' => 'Invalid currency code verification result'
           ];
       }

       // Check payment amount
       $totalCheckout = number_format($this->_checkout->price, 2, '.', '');
       if ($messageAmount != $totalCheckout) {
           return [
               'result' => 2,
               'content' => 'Invalid amount verification result'
           ];
       }

       // Check Fraud statuses and update payment & checkout data

       $this->_checkout->method_status = $messageFraudStatus;

       $this->_payment->response_status = $messageFraudStatus;
       $this->_payment->transaction_id = $messageInvoiceId;
       $this->_payment->email = $messageCustomerEmail;

       // Check fraud statuses
       if ($messageFraudStatus == self::FRAUD_STATUS_FAIL) {
           $this->_payment->status = Payments::STATUS_FAILED;

           return [
               'result' => 2,
               'content' => 'Fraud status Fail!'
           ];
       }

       if ($messageFraudStatus == self::FRAUD_STATUS_WAIT) {
           $this->_payment->status = Payments::STATUS_AWAITING;

           return [
               'result' => 2,
               'content' => 'Fraud status Wait!'
           ];
       }

       if ($messageFraudStatus == self::FRAUD_STATUS_PASS) {
           $this->_payment->status = Payments::STATUS_AWAITING;

           return [
               'result' => 1,
               'transaction_id' => $messageInvoiceId,
               'amount' => $this->_checkout->price,
               'checkout_id' => $this->_checkout->id,
           ];
       }

       return [
           'result' => 2,
           'content' => 'Unknown error!'
       ];
   }


    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function __old_processing($store)
    {
        $request = Yii::$app->request;
        $mergedRequestParams = array_merge($request->get(), $request->post());
        $paymentParams = array_change_key_case($mergedRequestParams, CASE_LOWER);

        $checkoutId = ArrayHelper::getValue($paymentParams, 'merchant_order_id');

        error_log(print_r($paymentParams, 1), 0);

        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_2CHECKOUT,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        // Logging PS request
        $this->log(json_encode($paymentParams, JSON_PRETTY_PRINT));

        // Check payment method
        if (empty($paymentMethod)) {
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getDetails();
        $secretWord = ArrayHelper::getValue($paymentMethodOptions, 'secret_word', null);

        // Check secret word
        if (!isset($secretWord)) {
            return [
                'result' => 2,
                'content' => 'secret word does not exist.'
            ];
        }

        // Check checkout
        if (empty($checkoutId)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $checkoutId,
                'method_id' => $paymentMethod->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'no invoice'
            ];
        }

        // Logging PS checkout request
        PaymentsLog::log($checkoutId, $paymentParams);

        // Check payment key
        $checkResult = $this->_checkPayment($secretWord, $paymentParams);
        if (!$checkResult) {
            $this->_payment->status = Payments::STATUS_FAILED;
            return [
                'result' => 2,
                'content' => 'Invalid payment verification result'
            ];
        }

        // Check payment currency
        if (strcasecmp($paymentParams['currency_code'], $store->currency) !== 0) {
            return [
                'result' => 2,
                'content' => 'Invalid currency code verification result'
            ];
        }

        // Check payment amount
        $totalCheckout = number_format($this->_checkout->price, 2, '.', '');
        if ($paymentParams['total'] != $totalCheckout) {
            return [
                'result' => 2,
                'content' => 'Invalid amount verification result'
            ];
        }

        // TODO:: this PS does not return payment status at all.
        // $this->_checkout->method_status = $paymentStatus;
        // $this->_payment->response_status = $paymentStatus;

        $transactionId = ArrayHelper::getValue($paymentParams, 'invoice_id');
        $payerEmail = ArrayHelper::getValue($paymentParams, 'email');

        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->transaction_id = $transactionId;
        $this->_payment->email = $payerEmail;

        return [
            'result' => 1,
            'transaction_id' => $transactionId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok',
        ];
    }

    /**
     * Check 2Checkout payment
     * @param $secretWord
     * @param array $paymentParams
     * @return bool
     */
    private function _checkPayment($secretWord, $paymentParams)
    {
        $requiredFields = ['sid', 'total', 'order_number', 'key', 'currency_code'];
        $isRequiredFieldsExist = !array_diff_key(array_flip($requiredFields), $paymentParams);

        if (!$isRequiredFieldsExist) {
            return false;
        }

        $hashSid = $paymentParams['sid'];
        $hashTotal = $paymentParams['total'];
        $hashOrder = $paymentParams['order_number'];
        $stringToHash = strtoupper(md5($secretWord . $hashSid . $hashOrder . $hashTotal));

        return $stringToHash != $paymentParams['key'];
    }

}