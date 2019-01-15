<?php

namespace sommerce\components\payments\methods;

use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\StorePaymentMethods;
use common\models\stores\Stores;
use Yii;
use sommerce\components\payments\BasePayment;
use common\helpers\SiteHelper;
use yii\helpers\ArrayHelper;

/**
 * Class Paypal
 * @package app\components\payments\methods
 */
class Paypal extends BasePayment
{
    /**
     * @var string - url action
     */
    public $action = 'https://www.paypal.com/cgi-bin/webscr';

    public $method = 'POST';

    /**
     * Указываем, куда будет отправляться запрос
     * Реальные условия - https://api-3t.paypal.com/nvp
     * Песочница - https://api-3t.sandbox.paypal.com/nvp
     * @var string
     */
    protected $endPoint = 'https://api-3t.paypal.com/nvp';

    /**
     * Указываем, куда будет отправляться запрос для клиента
     * Реальные условия - https://www.paypal.com/webscr
     * Песочница - https://www.sandbox.paypal.com/webscr
     * @var string
     */
    protected $paymentPoint = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=';

    /**
     * Версия API
     * @var string
     */
    protected $version = '95.0';

    public $redirectProcessing = true;

    /**
     * Init test mode
     */
    public function testMode()
    {
        $this->action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $this->endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
        $this->paymentPoint = 'https://www.sandbox.paypal.com/webscr';
    }

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

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $amount = number_format($checkout->price, 2, '.', '');

        // Now only express checkout
        $credentials = [
            'USER' => ArrayHelper::getValue($paymentMethodOptions, 'username'),
            'PWD' => ArrayHelper::getValue($paymentMethodOptions, 'password'),
            'SIGNATURE' => ArrayHelper::getValue($paymentMethodOptions, 'signature'),
        ];

        $requestParams = [
            'RETURNURL' => SiteHelper::hostUrl() . '/paypalexpress/' . $checkout->id,
            'CANCELURL' => SiteHelper::hostUrl() . '/cart'
        ];

        $orderParams = [
            'PAYMENTREQUEST_0_AMT' => $amount,
            'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
            'PAYMENTREQUEST_0_CURRENCYCODE' => $store->currency,
            'PAYMENTREQUEST_0_ITEMAMT' => $amount,
            'NOSHIPPING' => '1'
        ];


        $item = [
            'L_PAYMENTREQUEST_0_NAME0' => static::getDescription($checkout->id),
            'L_PAYMENTREQUEST_0_AMT0' => $amount,
            'L_PAYMENTREQUEST_0_QTY0' => '1'
        ];

        $response = $this->request('SetExpressCheckout', $credentials + $requestParams + $orderParams + $item);

        if (is_array($response) && $response['ACK'] == 'Success') { // Запрос был успешно принят
            $token = $response['TOKEN'];

            return static::returnRedirect($this->paymentPoint . '?cmd=_express-checkout&token=' . urlencode($token));
        }

        return static::returnError();
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array|mixed
     */
    public function processing($store)
    {
        $storePaymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_PAYPAL);

        if (empty($storePaymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentMethodOptions = $storePaymentMethod->getOptions();

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        return $this->expressProcessing($store, $storePaymentMethod);
    }

    /**
     * Processing express payments result
     * @param Stores $store
     * @param StorePaymentMethods $details
     * @return array
     */
    protected function expressProcessing($store, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        $token = ArrayHelper::getValue($_GET, 'token');
        $payerId = ArrayHelper::getValue($_GET, 'PayerID');
        $id = ArrayHelper::getValue($_GET, 'id');

        if (!$token || !$payerId || !$id) {
            return [
                'result' => 2,
                'content' => 'no data',
            ];
        }

        $credentials = [
            'USER' => ArrayHelper::getValue($paymentMethodOptions, 'username'),
            'PWD' => ArrayHelper::getValue($paymentMethodOptions, 'password'),
            'SIGNATURE' => ArrayHelper::getValue($paymentMethodOptions, 'signature'),
        ];

        $checkoutDetails = $this->request('GetExpressCheckoutDetails', $credentials + ['TOKEN' => $token]);


        $requestParams = [
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYERID' => $payerId,
            'TOKEN' => $token,
            'PAYMENTREQUEST_0_AMT' => ArrayHelper::getValue($checkoutDetails, 'PAYMENTREQUEST_0_AMT'),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $store->currency, // валюта панели
        ];

        $response = $this->request('DoExpressCheckoutPayment', $credentials + $requestParams);

        $this->log(json_encode($response, JSON_PRETTY_PRINT));

        if (empty($id)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $id,
                'method_id' => $details->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no invoice
            return [
                'checkout_id' => $id,
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
        } else if ($this->_payment->method != $this->_method) {
            // no invoice
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, [
            'DoExpressCheckoutPayment' => $response
        ]);

        if (!is_array($response) || strtolower(ArrayHelper::getValue($response, 'ACK', '')) != 'success') {
            // no invoice
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'bad response',
            ];
        }

        $transactionId = ArrayHelper::getValue($response, 'PAYMENTINFO_0_TRANSACTIONID');

        $GetTransactionDetails = $this->request('GetTransactionDetails', $credentials + [
            'TRANSACTIONID' => $transactionId
        ]);

        $this->log(json_encode($GetTransactionDetails, JSON_PRETTY_PRINT));

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, [
            'GetTransactionDetails' => $GetTransactionDetails
        ]);

        if (ArrayHelper::getValue($checkoutDetails, 'AMT') != $this->_checkout->price) { // сверяем сумму оплаты payments.amount
            // no invoice
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'amount fail',
            ];
        }

        if (ArrayHelper::getValue($GetTransactionDetails, 'CURRENCYCODE') != $this->_checkout->currency) {// проверяем валюту чека и ту что вернула платежка
            // no invoice
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'bad currency',
            ];
        }

        $getTransactionDetailsStatus = ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS', '');
        $doExpressCheckoutPaymentStatus = ArrayHelper::getValue($response, 'PAYMENTINFO_0_PAYMENTSTATUS', $getTransactionDetailsStatus);
        $getTransactionDetailsStatus = strtolower($getTransactionDetailsStatus);
        $doExpressCheckoutPaymentStatus = strtolower($doExpressCheckoutPaymentStatus);

        $this->_checkout->method_status = $getTransactionDetailsStatus;

        $this->_payment->transaction_id = $transactionId;
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $getTransactionDetailsStatus;
        $this->_payment->name = trim(ArrayHelper::getValue($GetTransactionDetails, 'FIRSTNAME') . ' ' . ArrayHelper::getValue($GetTransactionDetails, 'LASTNAME'));
        $this->_payment->email = ArrayHelper::getValue($GetTransactionDetails, 'EMAIL');
        $this->_payment->country = ArrayHelper::getValue($GetTransactionDetails, 'COUNTRYCODE');
        $this->_payment->fee = ArrayHelper::getValue($GetTransactionDetails, 'FEEAMT', 0);
        $this->_payment->memo = ArrayHelper::getValue($GetTransactionDetails, 'EMAIL') . '; ' . $transactionId;

        if ($getTransactionDetailsStatus != 'completed' || $getTransactionDetailsStatus != $doExpressCheckoutPaymentStatus) {
            // no invoice
            return [
                'checkout_id' => $id,
                'result' => 2,
                'content' => 'other status',
            ];
        }

        if (empty($GetTransactionDetails['EMAIL'])) {
            $GetTransactionDetails['EMAIL'] = '';
        }

        return [
            'result' => 1,
            'transaction_id' => $transactionId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
        ];
    }

    /**
     * Сформировываем запрос
     *
     * @param string $method Данные о вызываемом методе перевода
     * @param array $params Дополнительные параметры
     * @return array | boolean Response array | boolean false on failure
     */
    public function request($method, $params = [])
    {
        if (empty($method)) { // Проверяем, указан ли способ платежа
            return false;
        }

        // Параметры нашего запроса
        $requestParams = [
            'METHOD' => $method,
            'VERSION' => $this->version,
        ];

        // Сформировываем данные для NVP
        $request = http_build_query($requestParams + $params);

        // Настраиваем cURL
        $curlOptions = [
            CURLOPT_URL => $this->endPoint,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => Yii::getAlias('@common') . '/config/certificates/pp.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request,
        ];

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            $curlOptions += [
                CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
                CURLOPT_PROXY => PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        // Отправляем наш запрос, $response будет содержать ответ от API
        $response = curl_exec($ch);

        // Проверяем, нету ли ошибок в инициализации cURL
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        } else {
            curl_close($ch);
            $responseArray = [];
            parse_str($response, $responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
    }

    /**
     * Check payment status
     * @param Payments $payment
     * @param Stores $store
     * @param StorePaymentMethods $details
     * @return boolean
     */
    public function checkStatus($payment, $store, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $credentials = [
            'USER' => ArrayHelper::getValue($paymentMethodOptions, 'username'),
            'PWD' => ArrayHelper::getValue($paymentMethodOptions, 'password'),
            'SIGNATURE' => ArrayHelper::getValue($paymentMethodOptions, 'signature'),
        ];

        $GetTransactionDetails = $this->request('GetTransactionDetails', $credentials + [
            'TRANSACTIONID' => $payment->transaction_id
        ]);

        // заносим запись в таблицу payments_log
        PaymentsLog::log($payment->checkout_id, [
            'Cron.GetTransactionDetails' => $GetTransactionDetails
        ]);

        $status = (string)ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS', '');
        $status = strtolower(trim($status));
        $amount = ArrayHelper::getValue($GetTransactionDetails, 'AMT');
        $currency = ArrayHelper::getValue($GetTransactionDetails, 'CURRENCYCODE');
        $errorCode = ArrayHelper::getValue($GetTransactionDetails, 'L_ERRORCODE0');

        // [L_ERRORCODE0] => 10007 для этой ошибки переносим платеж в failed
        if (10007 == $errorCode) {
            $payment->status = Payments::STATUS_FAILED;
            $payment->save(false);
            return false;
        }

        // если стаутс не Completed и не Pending и не In-Progress тогда переводим invoice_status = 4
        if (!empty($status) && !in_array($status, ['completed', 'pending', 'in-progress'])) {
            $payment->status = Payments::STATUS_FAILED;
            $payment->save(false);
        }

        // Проверяемстатус, сумму и валюту
        if ($status != 'completed' || $amount != $payment->amount || $currency != $store->currency) {
            return false;
        }

        static::success($payment, [
            'result' => 1,
            'transaction_id' => $payment->transaction_id,
            'amount' => $payment->amount,
            'checkout_id' => $payment->checkout_id,
            'content' => 'Ok'
        ], $store);
    }
}