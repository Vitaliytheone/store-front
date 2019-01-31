<?php
namespace payments\methods;

use common\helpers\CurlHelper;
use common\models\gateways\PaymentMethods;
use common\models\gateway\Payments;
use Yii;
use payments\BasePayment;
use yii\helpers\ArrayHelper;

/**
 * Class Paypal
 * @package payments\methods
 */
class Paypal extends BasePayment
{
    // PayPal payment transaction statuses (lowercased)
    const PAYMENTSTATUS_NONE = 'none';
    const PAYMENTSTATUS_CANCELED_REVERSAL = 'canceled-reversal';
    const PAYMENTSTATUS_COMPLETED = 'completed';
    const PAYMENTSTATUS_DENIED = 'denied';
    const PAYMENTSTATUS_EXPIRED = 'expired';
    const PAYMENTSTATUS_FAILED = 'failed';
    const PAYMENTSTATUS_IN_PROGRESS = 'in-progress';
    const PAYMENTSTATUS_PARTIALLY_REFUNDED = 'partially-refunded';
    const PAYMENTSTATUS_PENDING = 'pending';
    const PAYMENTSTATUS_REFUNDED = 'refunded';
    const PAYMENTSTATUS_REVERSED = 'reversed';
    const PAYMENTSTATUS_PROCESSED = 'processed';
    const PAYMENTSTATUS_VOIDED = 'voided';
    const PAYMENTSTATUS_COMPLETED_FUNDS_HELD = 'completed-funds-held';

    const PAYER_STATUS_VERIFIED = 'verified';
    const PAYER_STATUS_UNVERIFIED = 'unverified';

    const ACCESS_STATUS_DENIED = 'permission denied';

    protected $_method_id = PaymentMethods::METHOD_PAYPAL;

    protected $_method = 'paypalexpress';

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
     * Current payment transaction details
     * @var array
     */
    protected $_transactionDetails;

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
     * Return available NVP PayPal payment statuses (lowercased)
     * @return array
     */
    public static function paymentStatuses()
    {
        return [
            static::PAYMENTSTATUS_NONE,
            static::PAYMENTSTATUS_CANCELED_REVERSAL,
            static::PAYMENTSTATUS_COMPLETED,
            static::PAYMENTSTATUS_DENIED,
            static::PAYMENTSTATUS_EXPIRED,
            static::PAYMENTSTATUS_FAILED,
            static::PAYMENTSTATUS_IN_PROGRESS,
            static::PAYMENTSTATUS_PARTIALLY_REFUNDED,
            static::PAYMENTSTATUS_PENDING,
            static::PAYMENTSTATUS_REFUNDED,
            static::PAYMENTSTATUS_REVERSED,
            static::PAYMENTSTATUS_PROCESSED,
            static::PAYMENTSTATUS_VOIDED,
            static::PAYMENTSTATUS_COMPLETED_FUNDS_HELD,
        ];
    }

    /**
     * @inheritdoc
     */
    public function checkouting()
    {
        $paymentMethodOptions = $this->getPaymentMethod()['options'];
        $description = $this->getDescription();

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $amount = number_format($this->getPayment()->amount, 2, '.', '');

        $credentials = [
            'USER' => ArrayHelper::getValue($paymentMethodOptions, 'username'),
            'PWD' => ArrayHelper::getValue($paymentMethodOptions, 'password'),
            'SIGNATURE' => ArrayHelper::getValue($paymentMethodOptions, 'signature'),
        ];

        $requestParams = [
            'RETURNURL' => $this->getNotifyUrl() . '/' . $this->getPayment()->id,
            'CANCELURL' => $this->getReturnUrl()
        ];

        $orderParams = [
            'PAYMENTREQUEST_0_AMT' => $amount,
            'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->getPayment()->currency,
            'PAYMENTREQUEST_0_ITEMAMT' => $amount,
            'PAYMENTREQUEST_0_DESC' => $description,
            //'NOSHIPPING' => 1,
        ];


        $item = [
            'L_PAYMENTREQUEST_0_NAME0' => $description,
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
     * @inheritdoc
     */
    public function processing()
    {
        $paymentMethodOptions = $this->getPaymentMethod()['options'];

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $token = ArrayHelper::getValue($_GET, 'token');
        $payerId = ArrayHelper::getValue($_GET, 'PayerID');
        $id = ArrayHelper::getValue($_GET, 'id');

        if (!$token || !$payerId || !$id) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        $this->getPaymentById($id);

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
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->getPayment()->currency, // валюта панели
        );

        $response = $this->request('DoExpressCheckoutPayment', $credentials + $requestParams);

        $this->fileLog(json_encode($response, JSON_PRETTY_PRINT));

        // заносим запись в таблицу payments_log
        $this->dbLog($this->getPayment(), json_encode([
            'DoExpressCheckoutPayment' => $response
        ], JSON_PRETTY_PRINT));

        if (!is_array($response) || strtolower(ArrayHelper::getValue($response, 'ACK', '')) != 'success') {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad response'
            ];
        }

        $transactionId = ArrayHelper::getValue($response, 'PAYMENTINFO_0_TRANSACTIONID');

        $this->_transactionDetails = $this->request('GetTransactionDetails', $credentials + [
            'TRANSACTIONID' => $transactionId
        ]);

        $this->fileLog(json_encode($this->_transactionDetails, JSON_PRETTY_PRINT));

        // заносим запись в таблицу payments_log
        $this->dbLog($this->getPayment(), json_encode([
            'GetTransactionDetails' => $this->_transactionDetails
        ], JSON_PRETTY_PRINT));

        if (ArrayHelper::getValue($checkoutDetails, 'AMT') != $this->getPayment()->amount) { // сверяем сумму оплаты payments.amount
            // no invoice
            return [
                'result' => 2,
                'content' => 'amount fail'
            ];
        }

        if (ArrayHelper::getValue($this->_transactionDetails, 'CURRENCYCODE') != $this->getPayment()->currency) {// проверяем валюту панели и ту что вернула платежка
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad currency'
            ];
        }

        $getTransactionDetailsStatus = ArrayHelper::getValue($this->_transactionDetails, 'PAYMENTSTATUS', '');
        $doExpressCheckoutPaymentStatus = ArrayHelper::getValue($response, 'PAYMENTINFO_0_PAYMENTSTATUS', $getTransactionDetailsStatus);
        $getTransactionDetailsStatus = strtolower($getTransactionDetailsStatus);
        $doExpressCheckoutPaymentStatus = strtolower($doExpressCheckoutPaymentStatus);

        $this->getPayment()->transaction_id = $transactionId;
        $this->getPayment()->status = Payments::STATUS_PENDING;
        $this->getPayment()->response_status = $getTransactionDetailsStatus;

        if ($getTransactionDetailsStatus != 'completed' || $getTransactionDetailsStatus != $doExpressCheckoutPaymentStatus) {

            if ('pending' == $getTransactionDetailsStatus || $getTransactionDetailsStatus != $doExpressCheckoutPaymentStatus) {
                $this->getPayment()->status = Payments::STATUS_WAITING;
            }

            // no invoice
            return [
                'result' => 2,
                'content' => 'other status'
            ];
        }

        if (empty($this->_transactionDetails['EMAIL'])) {
            $this->_transactionDetails['EMAIL'] = '';
        }

        return [
            'result' => 1,
            'transaction_id' => $transactionId,
            'amount' => $this->getPayment()->amount,
            'payment_id' => $this->getPayment()->id,
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
            CURLOPT_CAINFO          => Yii::getAlias('@common') . '/config/certificates/pp.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => $request,
        ];

        $ch = CurlHelper::curlInit();
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

    /**
     * @param $credentials
     * @param $transactionId
     * @return array|bool
     */
    public function getTransactionDetails($credentials, $transactionId)
    {
        return $this->request('GetTransactionDetails', $credentials + [
            'TRANSACTIONID' => $transactionId
        ]);
    }

    /**
     * Check payment status
     * @return bool
     * @internal param array $details
     */
    public function checkStatus()
    {
        $paymentMethodOptions = $this->getPaymentMethod()['options'];

        if (ArrayHelper::getValue($paymentMethodOptions, 'test_mode')) {
            $this->testMode();
        }

        $credentials = [
            'USER' => ArrayHelper::getValue($paymentMethodOptions, 'username'),
            'PWD' => ArrayHelper::getValue($paymentMethodOptions, 'password'),
            'SIGNATURE' => ArrayHelper::getValue($paymentMethodOptions, 'signature'),
        ];

        $GetTransactionDetails = $this->request('GetTransactionDetails', $credentials + [
            'TRANSACTIONID' => $this->getPayment()->transaction_id
        ]);

        $status = ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS', '');

        if ($this->getPayment()->response_status == $status) {
            $this->getPayment()->save(false);
            return false;
        }

        // заносим запись в таблицу payments_log
        $this->dbLog($this->getPayment(), [
            'Cron.GetTransactionDetails' => $GetTransactionDetails
        ]);

        $this->getPayment()->response_status = $status;

        $status = strtolower(trim($status));
        $amount = ArrayHelper::getValue($GetTransactionDetails, 'AMT');
        $currency = ArrayHelper::getValue($GetTransactionDetails, 'CURRENCYCODE');

        // если стаутс не Completed и не Pending и не In-Progress тогда переводим invoice_status = 4
        if (!empty($status) && !in_array($status, [
            static::PAYMENTSTATUS_COMPLETED,
            static::PAYMENTSTATUS_PENDING,
            static::PAYMENTSTATUS_IN_PROGRESS,
        ])) {
            $this->getPayment()->status = Payments::STATUS_FAIL;
        }

        $this->getPayment()->save(false);

        // Проверяемстатус, сумму и валюту
        if ($status != static::PAYMENTSTATUS_COMPLETED || $amount != $this->getPayment()->amount || $currency != $this->getPayment()->currency) {
            return false;
        }

        $this->success([
            'result' => 1,
            'transaction_id' => $this->getPayment()->transaction_id,
            'amount' => $this->getPayment()->amount,
            'payment_id' => $this->getPayment()->id,
        ]);
    }
}