<?php

namespace sommerce\components\payments\methods;

use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use common\models\store\PaymentsLog;
use common\models\store\Payments;
use yii\helpers\ArrayHelper;
use common\models\stores\StorePaymentMethods;

/**
 * Class Paywant
 * @package sommerce\components\payments\methods
 */
class Paytr extends BasePayment
{
    /**
     * @var string - url action
     */
    public $action = 'https://www.paytr.com/odeme/';

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
        $options = $checkout->getUserDetails();

        $merchantId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_id');
        $merchantKey = ArrayHelper::getValue($paymentMethodOptions, 'merchant_key');
        $merchantSalt = ArrayHelper::getValue($paymentMethodOptions, 'merchant_salt');
        $merchantCommission = (float)ArrayHelper::getValue($paymentMethodOptions, 'commission', 0);

        if (!$merchantId || !$merchantKey|| !$merchantSalt) {
            return static::returnError();
        }

        $paymentAmountTl = (float)$checkout->price;
        $paymentCommission = $paymentAmountTl + $paymentAmountTl * ($merchantCommission / 100);
        $paymentAmount = $paymentCommission * 100;


        $id = $checkout->id;
        $userName = $email;
        $userAddress = "-";
        $userPhone = ArrayHelper::getValue($options, 'phone', "-");
        $merchantSuccess = SiteHelper::hostUrl() . '/addfunds';
        $merchantFail = SiteHelper::hostUrl() . '/addfunds';

        $userBasket = base64_encode(json_encode(array(
            array("BAKIYE YUKLEME", $paymentAmount, 1),
        )));

        $userIp = static::getIp();
        $timeoutLimit = "30";
        $debugOn = 0;
        $testMode = 0;
        $noInstallment	= 0;
        $maxInstallment = 0;
        $currency = $store->currency;

        $hash = $merchantId . $userIp . $id . $email . $paymentAmount . $userBasket . $noInstallment . $maxInstallment . $currency . $testMode;
        $token = base64_encode(hash_hmac('sha256', $hash . $merchantSalt, $merchantKey, true));

        $post = [
            'merchant_id' => $merchantId,
            'user_ip' => $userIp,
            'merchant_oid' => $id,
            'email' => $email,
            'payment_amount' => $paymentAmount,
            'paytr_token' => $token,
            'user_basket' => $userBasket,
            'debug_on' => $debugOn,
            'no_installment' => $noInstallment,
            'max_installment' => $maxInstallment,
            'user_name' => $userName,
            'user_address' => $userAddress,
            'user_phone' => $userPhone,
            'merchant_ok_url' => $merchantSuccess,
            'merchant_fail_url' => $merchantFail,
            'timeout_limit' => $timeoutLimit,
            'currency' => $currency,
            'test_mode' => $testMode
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->action . "api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']);
        }

        $result = @curl_exec($ch);

        if (curl_errno($ch)) {
            return static::returnError();
        }

        curl_close($ch);

        $result = json_decode($result, true);

        if ('success' != $result['status']) {
            return static::returnError();

        }

        $redirectUrl = $this->action . 'guvenli/' . $result['token'] . '?paytr_token=' . $token;

        return self::returnRedirect($redirectUrl);
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $this->showErrors = true;

        $this->log(json_encode($_POST, JSON_PRETTY_PRINT));

        $checkoutId = ArrayHelper::getValue($_POST, 'merchant_oid');
        $status = ArrayHelper::getValue($_POST, 'status');
        $amount = ArrayHelper::getValue($_POST, 'payment_amount');
        $hash = ArrayHelper::getValue($_POST, 'hash');
        $currency = ArrayHelper::getValue($_POST, 'currency');
        $totalAmount = ArrayHelper::getValue($_POST, 'total_amount');

        $transactionId = null;

        if (!$checkoutId || !$status || !$amount || !$hash || !$currency) {
            return [
                'result' => 2,
                'content' => 'OK',
                'reason' => 'no data',
            ];
        }

        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_PAYTR);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

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
                'content' => 'OK',
                'reason' => 'no invoice',
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
                'checkout_id' => $checkoutId,
                'result' => 2,
                'content' => 'OK',
                'reason' => 'no invoice',
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();

        $merchantId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_id');
        $merchantKey = ArrayHelper::getValue($paymentMethodOptions, 'merchant_key');
        $merchantSalt = ArrayHelper::getValue($paymentMethodOptions, 'merchant_salt');

        if (!$merchantId || !$merchantKey || !$merchantSalt) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'OK',
                'reason' => 'bad payment method',
            ];
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $_POST);

        $generatedHash = base64_encode(hash_hmac('sha256', $checkoutId . $merchantSalt . $status . $totalAmount , $merchantKey, true));

        if ($hash != $generatedHash) {
            return [
                'result' => 2,
                'content' => 'OK',
                'reason' => 'bad hash',
            ];
        }

        if ($this->_checkout->price != $amount && $this->_checkout->price != $totalAmount) {
            return [
                'result' => 2,
                'content' => 'OK',
                'reason' => 'bad amount',
            ];
        }

        if (strtolower($currency) != 'tl') {
            return [
                'result' => 2,
                'content' => 'OK',
                'reason' => 'bad currency'
            ];
        }

        $this->_payment->transaction_id = $transactionId;
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $status;

        if ('success' != $status) {
            return [
                'result' => 2,
                'content' => 'OK',
                'reason' => 'no final status',
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $transactionId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }

    /**
     * Get user ip
     * @return string
     */
    protected static function getIp()
    {
        if (isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        return $ip;
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'phone' => [
                'label' => 'cart.phone',
                'type' => 'input',
                'rules' => [
                    ['phone', 'required', 'message' => 'cart.error.phone'],
                    ['phone', 'string', 'message' => 'cart.error.phone']
                ]
            ]
        ];
    }
}