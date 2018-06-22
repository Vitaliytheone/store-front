<?php
namespace sommerce\components\payments\methods;

use common\helpers\SiteHelper;
use common\models\store\Carts;
use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use net\authorize\api\contract\v1\ANetApiResponseType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\GetTransactionDetailsRequest;
use net\authorize\api\contract\v1\GetTransactionDetailsResponse;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionDetailsType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;
use net\authorize\api\controller\GetTransactionDetailsController;
use sommerce\helpers\AssetsHelper;
use Yii;
use sommerce\components\payments\BasePayment;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\OpaqueDataType;
use net\authorize\api\constants\ANetEnvironment;
use yii\helpers\ArrayHelper;

/**
 * Class Authorize
 * @package sommerce\components\payments\methods
 */
class Authorize extends BasePayment {

    /**
     * @var MerchantAuthenticationType
     */
    private $_auth;

    /**
     * @var string - url action
     */
    public $action = '';

    /**
     * @var string - url action
     */
    public $script = 'https://js.authorize.net/v3/AcceptUI.js';

    public function __construct(array $config = [])
    {
        $this->action = ANetEnvironment::PRODUCTION;

        if (!empty(Yii::$app->params['testAuthorize'])) {
            $this->initTestMode();
        }

        return parent::__construct($config);
    }

    /**
     * Init test mode settings
     */
    protected function initTestMode()
    {
        $this->action = ANetEnvironment::SANDBOX;
        $this->script = 'https://jstest.authorize.net/v3/AcceptUI.js';
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
        $options = $checkout->getUserDetails();

        if (!empty($paymentMethodOptions['test_mode'])) {
            $this->initTestMode();
        }

        $merchantLoginId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_login_id');
        $merchantTransactionKey = ArrayHelper::getValue($paymentMethodOptions, 'merchant_transaction_id');
        $clientKey = ArrayHelper::getValue($paymentMethodOptions, 'merchant_client_key');
        $dataDescriptor = ArrayHelper::getValue($options, 'data_descriptor');
        $dataValue = ArrayHelper::getValue($options, 'data_value');

        if (!$merchantLoginId || !$merchantTransactionKey || !$clientKey || !$dataDescriptor || !$dataValue) {
            $this->log([
                'result' => 2,
                'content' => 'bad data'
            ]);
            return static::returnError();
        }

        // Set the transaction's refId
        $refId = 'ref' . $checkout->id;
        // Create the payment data from a Visa Checkout blob
        $op = new OpaqueDataType();
        $op->setDataDescriptor($dataDescriptor);
        $op->setDataValue($dataValue);
        $op->setDataKey($clientKey);

        $paymentOne = new PaymentType();
        $paymentOne->setOpaqueData($op);

        //create a transaction
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($checkout->price);
        $transactionRequestType->setCallId(time());
        $transactionRequestType->setPayment($paymentOne);
        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($this->getAuth($details));
        $request->setRefId( $refId);
        $request->setTransactionRequest( $transactionRequestType);
        $controller = new CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($this->action);

        /**
         * @var $response ANetApiResponseType
         */
        if (empty($response)) {
            return static::returnError([
                'result' => 3,
                'content' => 'bad data'
            ]);
        }

        $responseMessage = $response->getMessages();
        if ('Ok' != $responseMessage->getResultCode()) {
            return static::returnError([
                'result' => 3,
                'content' => 'bad status'
            ]);
        }

        $response = $response->getTransactionResponse();

        if ($response === null || $response->getMessages() === null) {
            return static::returnError([
                'result' => 3,
                'content' => 'bad response data'
            ]);
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($checkout->id, $response);

        $transactionId = $response->getTransId();

        if (empty($transactionId)) {
            return static::returnError([
                'result' => 3,
                'content' => 'bad response data'
            ]);
        }

        if (!($this->_payment = Payments::findOne([
            'checkout_id' => $checkout->id,
        ]))) {
            $this->_payment = new Payments();
            $this->_payment->method = $this->_method;
            $this->_payment->checkout_id = $checkout->id;
            $this->_payment->amount = $checkout->price;
            $this->_payment->customer = $checkout->customer;
            $this->_payment->currency = $checkout->currency;
        }

        $this->_payment->transaction_id = $transactionId;
        $this->_payment->save(false);

        // Clear cart after payment will be create
        Carts::clearCheckoutItems($checkout);

        $response = $this->getTransactionDetails($transactionId, $this->_payment, $details);

        if (null == $response) {
            return static::returnError([
                'result' => 3,
                'content' => 'bad transaction response data',
            ]);
        }

        /**
         * @var TransactionDetailsType $transaction
         */
        $transaction = $response->getTransaction();
        $status = $transaction->getTransactionStatus();
        $responseCode = (int)$transaction->getResponseCode();

        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $status;
        $this->_payment->save(false);

        if (!in_array(strtolower($status), [
            'settledsuccessfully',
            'capturedpendingsettlement'
        ]) || 1 !== $responseCode) {

            return static::returnError([
                'result' => 2,
                'redirect' => SiteHelper::hostUrl() . '/' . PaymentMethods::METHOD_AUTHORIZE . '?checkout_id=' . $checkout->id,
                'content' => 'no final status',
            ]);
        }

        $result = [
            'result' => 1,
            'transaction_id' => $transactionId,
            'fee' => 0,
            'amount' => $this->_payment->amount,
            'checkout_id' => $this->_payment->checkout_id,
            'content' => 'Ok'
        ];

        $this->log($result);

        static::success($this->_payment, $result, $store);

        return static::returnRedirect(SiteHelper::hostUrl() . '/' . PaymentMethods::METHOD_AUTHORIZE . '?checkout_id=' . $checkout->id);
    }

    /**
     * Get js payment environment
     * @param Stores $store
     * @param string $email
     * @param PaymentMethods $details
     * @return array
     */
    public function getJsEnvironments($store, $email, $details)
    {
        $paymentMethodOptions = $details->getDetails();
        $clientKey = ArrayHelper::getValue($paymentMethodOptions, 'merchant_client_key');
        $loginId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_login_id');

        if (!empty($paymentMethodOptions['test_mode'])) {
            $this->initTestMode();
        }

        AssetsHelper::addCustomScriptFile($this->script);

        return [
            'type' => $details->id,
            'configure' => [
                'type' => 'button',
                'class' => 'AcceptUI',
                'data-billingAddressOptions' => ['show' => true, 'required' => false],
                'data-apiLoginID' => $loginId,
                'data-clientKey' => $clientKey,
                'data-acceptUIFormBtnTxt' => Yii::t('app', 'cart.button.checkout'),
                'data-acceptUIFormHeaderTxt' => $store->name,
                'data-responseHandler' => 'responseAuthorizeHandler',
            ],
        ];
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        return [
            'result' => 2,
            'checkout_id' => ArrayHelper::getValue($_GET, 'checkout_id')
        ];
    }

    /**
     * Get payment method auth
     * @param PaymentMethods $details
     * @return MerchantAuthenticationType
     */
    public function getAuth($details)
    {
        if ($this->_auth) {
            return $this->_auth;
        }

        $paymentMethodOptions = $details->getDetails();

        if (!empty($paymentMethodOptions['test_mode'])) {
            $this->initTestMode();
        }

        $merchantLoginId = ArrayHelper::getValue($paymentMethodOptions, 'merchant_login_id');
        $merchantTransactionKey = ArrayHelper::getValue($paymentMethodOptions, 'merchant_transaction_id');

        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $this->_auth = new MerchantAuthenticationType();
        $this->_auth->setName($merchantLoginId);
        $this->_auth->setTransactionKey($merchantTransactionKey);

        return $this->_auth;
    }

    /**
     * Get transaction details
     * @param string $transactionId
     * @param Payments $payment
     * @param PaymentMethods $details
     * @return AnetApiResponseType|null
     */
    public function getTransactionDetails($transactionId, $payment, $details)
    {
        $request = new GetTransactionDetailsRequest();
        $request->setMerchantAuthentication($this->getAuth($details));
        $request->setTransId($transactionId);
        $controller = new GetTransactionDetailsController($request);
        $response = $controller->executeWithApiResponse($this->action);

        if (($response == null) || ($response->getMessages()->getResultCode() != "Ok")) {
            return null;
        }

        // Logging PS checkout request
        PaymentsLog::log($payment->checkout_id, $response->getTransaction());

        return $response;
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
        if (empty($payment->transaction_id)) {
            return false;
        }

        if (($response = $this->getTransactionDetails($payment->transaction_id, $payment, $details))) {
            /**
             * @var TransactionDetailsType $transaction
             * @var GetTransactionDetailsResponse $response
             */
            $transaction = $response->getTransaction();
            $status = $transaction->getTransactionStatus();
            $responseCode = (int)$transaction->getResponseCode();

            if (!in_array(strtolower($status), [
                'settledsuccessfully',
                'capturedpendingsettlement',
            ]) || 1 !== $responseCode) {
                return false;
            }

            $payment->response_status = $status;
            $payment->save(false);

            static::success($payment, [
                'result' => 1,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'checkout_id' => $payment->checkout_id,
                'content' => 'OK'
            ], $store);
        }
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'data_descriptor' => [
                'name' => 'data_descriptor',
                'type' => 'hidden',
            ],
            'data_value' => [
                'name' => 'data_value',
                'type' => 'hidden',
            ],
        ];
    }
}