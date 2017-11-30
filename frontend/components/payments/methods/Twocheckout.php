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
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class Twocheckout
 * @package app\components\payments\methods
 */
class Twocheckout extends BasePayment {

    public $action;
    public $method = 'GET';

    const TEST_MODE_ON = 1;
    const MODE_TEST_OFF = 0;

    const HOSTED_URL_SANDBOX = 'https://sandbox.2checkout.com/checkout/purchase';
    const HOSTED_URL_PRODUCTION = 'https://www.2checkout.com/checkout/purchase';

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

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $request = Yii::$app->request;
        $mergedRequestParams = array_merge($request->get(), $request->post());
        $paymentParams = array_change_key_case($mergedRequestParams, CASE_LOWER);

        $checkoutId = ArrayHelper::getValue($paymentParams, 'merchant_order_id');

        // Logging PS request
        PaymentsLog::log($checkoutId, json_encode($paymentParams, JSON_PRETTY_PRINT));
        $this->log(json_encode($paymentParams, JSON_PRETTY_PRINT));

        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_2CHECKOUT,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        $paymentMethodOptions = $paymentMethod->getDetails();
        $secretWord = ArrayHelper::getValue($paymentMethodOptions, 'secret_word', null);

        // Check payment method
        if (empty($paymentMethod) || !isset($secretWord)) {
            return [
                'result' => 2,
                'content' => 'bad payment method'
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

        // Check payment
        $checkResult = $this->_checkPayment($secretWord, $paymentParams);
        if (!$checkResult) {
            $this->_payment->status = Payments::STATUS_FAILED;
            return [
                'result' => 2,
                'content' => 'Invalid payment verification result'
            ];
        }

        $transactionId = ArrayHelper::getValue($paymentParams, 'invoice_id');
        $payerEmail = ArrayHelper::getValue($paymentParams, 'email');

        // TODO:: this PS does not return payment status at all.
        // $this->_checkout->method_status = $paymentStatus;
        // $this->_payment->response_status = $paymentStatus;

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
     * @return bool
     * @param array $paymentParams
     */
    private function _checkPayment($secretWord, $paymentParams)
    {
        $requiredFields = ['sid', 'total', 'order_number', 'key'];
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