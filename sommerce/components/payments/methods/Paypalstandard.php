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

/**
 * Class Paypal Standart
 * @package app\components\payments\methods
 */
class Paypalstandard extends BasePayment {

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
    protected $paymentPoint = 'https://www.paypal.com/webscr';

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
     * @param PaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getDetails();

        Yii::debug($paymentMethodOptions);
        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $amount = number_format($checkout->price, 2, '.', '');

        return static::returnForm($this->getFrom(), [
            'item_number' => $checkout->id,
            'cmd' => '_xclick',
            'business' => ArrayHelper::getValue($paymentMethodOptions, 'email'),
            'currency_code' => $store->currency,
            'return' => SiteHelper::hostUrl() . '/cart',
            'notify_url' => SiteHelper::hostUrl() . '/paypalstandard/' . $checkout->id,
            'cancel_return' => SiteHelper::hostUrl() . '/cart',
            'item_name' => static::getDescription($checkout->id),
            'amount' => $amount,
        ]);

//        return static::returnError();
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array | boolean
     */
    public function processing($store)
    {
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_PAYPAL_STANDARD,
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

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

            $this->_method = 'paypalstandard';
            return $this->standardProcessing($store, $paymentMethod);
    }


    /**
     * Processing standard payments result
     * @param Stores $store
     * @param PaymentMethods $details
     * @return array | boolean false on failure
     */
    protected function standardProcessing($store, $details)
    {
        // paypal standard отвечаем ok если платеж добавлен
        $this->redirectProcessing = false;

        $itemNumber = ArrayHelper::getValue($_POST, 'item_number', ArrayHelper::getValue($_GET, 'id'));
        $business = ArrayHelper::getValue($_POST, 'business');
        $paymentStatus = ArrayHelper::getValue($_POST, 'payment_status');
        $mcGross = ArrayHelper::getValue($_POST, 'mc_gross');
        $txnId = ArrayHelper::getValue($_POST, 'txn_id');
        $mcCurrency = ArrayHelper::getValue($_POST, 'mc_currency');
        $paypalAmount = ArrayHelper::getValue($_POST, 'mc_gross');
        $tax = ArrayHelper::getValue($_POST, 'tax');
        $payerEmail = ArrayHelper::getValue($_POST, 'payer_email');
        $id = ArrayHelper::getValue($_GET, 'id');

        if (!$itemNumber || !$business || !$paymentStatus || !$mcGross || !$txnId) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        $paymentMethodOptions = $details->getDetails();

        if (strtolower($business) != strtolower(ArrayHelper::getValue($paymentMethodOptions, 'email'))) {
            return [
                'result' => 2,
                'content' => 'incorrect data'
            ];
        }

        if (empty($id)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $itemNumber,
                'method_id' => $details->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PENDING])) {
            // no invoice
            return [
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
        PaymentsLog::log($this->_checkout->id, $_POST);

        $this->log(json_encode($_POST, JSON_PRETTY_PRINT));

        if (strtoupper($mcCurrency) != $store->currency) {
            return [
                'result' => 2,
                'content' => 'bad currency'
            ];
        }

        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        $get_magic_quotes_exists = false;
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        $ch = curl_init($this->action);
        if ($ch == FALSE) {
            return FALSE;
        }

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        curl_setopt($ch, CURLOPT_CAINFO, Yii::getAlias('@common') . '/config/certificates/pp.pem'); // сертификат стандартный для paypal тоже есть в my

        $res = curl_exec($ch);

        if (curl_errno($ch) != 0) {
            Yii::error(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL); // заносим в наш лог с уникальныйм номером адресом сайта и датой
            curl_close($ch);
            exit;
        } else {
            Yii::info(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 'paypalstandard');// заносим в наш лог с уникальныйм номером адресом сайта и датой
            Yii::info(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 'paypalstandard'); // заносим в наш лог с уникальныйм номером адресом сайта и датой
            curl_close($ch);
        }

        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));


        $this->log(json_encode($res, JSON_PRETTY_PRINT));

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $res);


        if (strcmp($res, "VERIFIED") != 0) {
            return [
                'result' => 2,
                'content' => 'bad payment'
            ];
        }

        if ($tax > 0) {
            $paypalAmount = $paypalAmount - $tax;
        }

        if ($paypalAmount != $this->_checkout->price) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_checkout->method_status = $paymentStatus;

        $this->_payment->transaction_id = $txnId;
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $paymentStatus;
        $this->_payment->email = $payerEmail;
        $this->_payment->memo = $payerEmail . '; ' . $txnId;

        if (isset($_POST['mc_fee'])) {
            $this->_payment->fee = $_POST['mc_fee'];
        }

        if (strtolower($paymentStatus) != 'completed') {
            return [
                'result' => 2,
                'content' => 'other status'
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $txnId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }

    /**
     * Сформировываем запрос
     *
     * @param string $method Данные о вызываемом методе перевода
     * @param array $params Дополнительные параметры
     * @return array | boolean Response array | boolean false on failure
     */
    public function request($method, $params = []) {
        if( empty($method) ) { // Проверяем, указан ли способ платежа
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
            CURLOPT_URL             => $this->endPoint,
            CURLOPT_SSL_VERIFYPEER  => 1,
            CURLOPT_SSL_VERIFYHOST  => 2,
            CURLOPT_CAINFO => Yii::getAlias('@common') . '/config/certificates/pp.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $request,
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
            parse_str($response,$responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
    }

    /**
     * Check payment status
     * @param Payments $payment
     * @param Stores $store
     * @param PaymentMethods $details
     * @return boolean
     */
    public function checkStatus($payment, $store, $details)
    {
        $paymentMethodOptions = $details->getDetails();

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

        // Проверяем статус, сумму и валюту
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