<?php

namespace sommerce\components\payments\methods;

use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use Yii;
use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use yii\helpers\ArrayHelper;
use common\models\stores\StorePaymentMethods;

/**
 * Class Twocheckout
 * 2Checkout payment Notification processing routine
 * https://www.2checkout.com/documentation/seller-area-manage-sales-fraud-review#
 *
 * @package app\components\payments\methods
 */
class Twocheckout extends BasePayment
{
    public $action;
    public $method = 'GET';
    public $redirectProcessing = false;
    public $showErrors = false;

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

    /**
     * Checkout
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getOptions();
        $mode = (int)ArrayHelper::getValue($paymentMethodOptions, 'test_mode', null);
        $accountNumber = ArrayHelper::getValue($paymentMethodOptions, 'account_number', null);
        $secretWord = ArrayHelper::getValue($paymentMethodOptions, 'secret_word', null);

        if (!isset($mode, $accountNumber, $secretWord)) {
            return static::returnError();
        }

        $amount = number_format($checkout->price, 2, '.', '');
        $receiptLinkUrl = SiteHelper::hostUrl() . '/cart';

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
            'li_0_name' => static::getDescription($checkout->id),
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

       $this->log(json_encode($requestParams, JSON_PRETTY_PRINT));

       $messageType = ArrayHelper::getValue($requestParams, 'message_type', null);              // 2Checkout message type
       $messageFraudStatus = ArrayHelper::getValue($requestParams, 'fraud_status', null);       // Payment fraud status
       $messageVendorOrderId = ArrayHelper::getValue($requestParams, 'vendor_order_id', null);  // Our checkout id

       $messageCustomerCurrency = ArrayHelper::getValue($requestParams, 'cust_currency', null);     // Customer currency
       $messageListCurrency = ArrayHelper::getValue($requestParams, 'list_currency', null);     // Our checkout currency

       $messageCustomerAmount = ArrayHelper::getValue($requestParams, 'invoice_cust_amount', null); // Customer currency amount
       $messageListAmount = ArrayHelper::getValue($requestParams, 'invoice_list_amount', null); // Our checkout currency amount

       $messageHash = ArrayHelper::getValue($requestParams, 'md5_hash', null);                  // Check sum
       $messageSaleId = ArrayHelper::getValue($requestParams, 'sale_id', null);                 // 2Checkout sale id
       $messageVendorId = ArrayHelper::getValue($requestParams, 'vendor_id', null);             // Our 2Checkout merchant id
       $messageInvoiceId = ArrayHelper::getValue($requestParams, 'invoice_id', null);           // 2Checkout invoice id
       $messageCustomerEmail = ArrayHelper::getValue($requestParams, 'customer_email', null);   // Customer email (payment email)

       $messageCustomerName = ArrayHelper::getValue($requestParams, 'customer_name', '');           // Customer full name
       $messageCustomerCountry = ArrayHelper::getValue($requestParams, 'customer_ip_country', ''); // Customer country by ip

       if (!isset($messageType, $messageFraudStatus, $messageVendorOrderId, $messageListCurrency, $messageListAmount, $messageHash, $messageSaleId, $messageVendorId, $messageInvoiceId)) {
           return [
               'result' => 3,
               'content' => "Invalid 2Checkout messages params!"
           ];
       }

       if (!in_array($messageType, static::$_allowedMessageTypes)) {
           return [
               'result' => 3,
               'content' => "Unknown 2Checkout message type!"
           ];
       }

       if (!in_array($messageFraudStatus, static::$_allowedFraudStatuses)) {
           return [
               'result' => 3,
               'content' => "Unknown 2Checkout fraud status!"
           ];
       }

       $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_2CHECKOUT);

       if (empty($paymentMethod)) {
           return [
               'result' => 3,
               'content' => 'Bad payment method!'
           ];
       }

       $paymentDetails = $paymentMethod->getOptions();
       $secretWord = ArrayHelper::getValue($paymentDetails, 'secret_word', null);

       if (empty($secretWord)) {
           return [
               'result' => 3,
               'content' => 'Invalid payment settings. Secret word does not exist!'
           ];
       }

       // Validate 2Checkout message
       $hashForMatch = strtoupper(md5($messageSaleId . $messageVendorId . $messageInvoiceId . $secretWord));
       if ($messageHash != $hashForMatch) {
           return [
               'result' => 3,
               'content' => 'Invalid 2Checkout message checksum!'
           ];
       }

       // Check checkout
       if (empty($messageVendorOrderId)
           || !($this->_checkout = Checkouts::findOne([
               'id' => $messageVendorOrderId,
               'method_id' => $paymentMethod->method_id
           ]))
           || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
           // no checkout
           return [
               'result' => 3,
               'content' => "Checkout #$messageVendorOrderId does not exist or already paid!"
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
               'checkout_id' => $messageVendorOrderId,
               'result' => 2,
               'content' => 'bad invoice payment'
           ];
       }

       // Logging PS checkout request
       PaymentsLog::log($this->_checkout->id, $requestParams);

       // Check invoice currency. Binary safe case-insensitive.
       if (strcasecmp($messageListCurrency, $this->_checkout->currency) !== 0) {
           return [
               'result' => 3,
               'content' => "Invalid checkout currency code verification result! Expected:" . $this->_checkout->currency . ", Current: $messageListCurrency"
           ];
       }

       // Check payment amount
       $totalCheckout = number_format($this->_checkout->price, 2, '.', '');
       if ($messageListAmount != $totalCheckout) {
           return [
               'result' => 3,
               'content' => "Invalid amount verification result! Expected: $totalCheckout, Current: $messageListAmount"
           ];
       }

       // Use new payment model for new 2CO order or exiting for update status
       if ($messageType == self::MESSAGE_TYPE_ORDER_CREATED) {

           $this->_payment->status = Payments::STATUS_AWAITING;
           $this->_payment->transaction_id = $messageInvoiceId;
           $this->_payment->memo = $messageInvoiceId;
           $this->_payment->email = $messageCustomerEmail;
           $this->_payment->name = trim($messageCustomerName);
           $this->_payment->country = $messageCustomerCountry;

       } else {

           $this->_payment = Payments::findOne(['checkout_id' => $this->_checkout->id]);

           if (!$this->_payment) {
               return [
                   'result' => 3,
                   'content' => "Expected Payment model id$this->_checkout->id. Null given!"
               ];
           }
       }

       if (in_array($this->_payment->status, [Payments::STATUS_FAILED, Payments::STATUS_COMPLETED])) {
           exit ('Unexpected 2Checkout status changes!');
       }

       $this->_checkout->method_status = $messageFraudStatus;
       $this->_payment->response_status = $messageFraudStatus;

       // Check fraud statuses
       if ($messageFraudStatus == self::FRAUD_STATUS_FAIL) {
           $this->_payment->status = Payments::STATUS_FAILED;
           $this->_payment->save(false);

           return [
               'result' => 3,
               'content' => 'Fraud status Fail!'
           ];
       }

       if ($messageFraudStatus == self::FRAUD_STATUS_WAIT) {
           $this->_payment->status = Payments::STATUS_AWAITING;
           $this->_payment->save(false);

           return [
               'result' => 3,
               'content' => 'Fraud status Wait!'
           ];
       }

       if ($messageFraudStatus == self::FRAUD_STATUS_PASS) {
           $this->_payment->status = Payments::STATUS_COMPLETED;
           $this->_payment->save(false);

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

}