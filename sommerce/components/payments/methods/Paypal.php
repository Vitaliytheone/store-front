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
 * Class Paypal
 * @package app\components\payments\methods
 */
class Paypal extends BasePayment {

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
     * @param PaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getDetails();

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $amount = number_format($checkout->price, 2, '.', '');

        // Now only express checkout
        if (0) {
            return static::returnForm($this->getFrom(), [
                'item_number' => $checkout->id,
                'cmd' => '_xclick',
                'business' => ArrayHelper::getValue($paymentMethodOptions, 'email'),
                'currency_code' => $store->currency,
                'return' => SiteHelper::hostUrl() . '/cart',
                'notify_url' => SiteHelper::hostUrl() . '/paypalstandart/' . $checkout->id,
                'cancel_return' => SiteHelper::hostUrl() . '/cart',
                'item_name' => static::getDescription($checkout->id),
                'amount' => $amount,
            ]);
        } else {
            $credentials = [
                'USER' => ArrayHelper::getValue($paymentMethodOptions, 'username'),
                'PWD' => ArrayHelper::getValue($paymentMethodOptions, 'password'),
                'SIGNATURE' => ArrayHelper::getValue($paymentMethodOptions, 'signature'),
            ];

            $requestParams = [
                'RETURNURL' => SiteHelper::hostUrl() . '/paypalexpress/' . $checkout->id,
                'CANCELURL' => SiteHelper::hostUrl() . '/addfunds'
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

        }
        return static::returnError();
    }

    /**
     * Processing payments result
     * @param Stores $store
     */
    public function processing($store)
    {
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_PAYPAL,
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

        // Now only express checkout
        if (0) {
            $this->_method = 'paypalstandart';
            return $this->standardProcessing($store, $paymentMethod);
        } else {
            $this->_method = 'paypal';
            return $this->expressProcessing($store, $paymentMethod);
        }
    }

    /**
     * Processing standart payments result
     * @param Stores $store
     * @param PaymentMethods $details
     */
    protected function expressProcessing($store, $details)
    {
        $paymentMethodOptions = $details->getDetails();

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


        $requestParams = array(
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYERID' => $payerId,
            'TOKEN' => $token,
            'PAYMENTREQUEST_0_AMT' => ArrayHelper::getValue($checkoutDetails, 'PAYMENTREQUEST_0_AMT'),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $store->currency, // валюта панели
        );

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
            if ('pending' == $getTransactionDetailsStatus || $getTransactionDetailsStatus != $doExpressCheckoutPaymentStatus) {
                $this->_payment->status = Payments::STATUS_FAILED;
            }

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
     * Processing standart payments result
     * @param Stores $store
     * @param PaymentMethods $details
     */
    protected function standardProcessing($store, $details)
    {
        // paypal standart отвечаем ok если платеж добавлен
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

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        // Отправляем наш запрос, $response будет содержать ответ от API
        $response = curl_exec($ch);

        // Проверяем, нету ли ошибок в инициализации cURL
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        } else  {
            curl_close($ch);
            $responseArray = [];
            parse_str($response,$responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
    }
}