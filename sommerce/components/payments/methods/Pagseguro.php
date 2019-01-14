<?php

namespace sommerce\components\payments\methods;

use common\models\store\PaymentsLog;
use common\models\store\Payments;
use Yii;
use common\helpers\SiteHelper;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use sommerce\components\payments\BasePayment;
use yii\helpers\ArrayHelper;
use PagSeguroPaymentRequest;
use PagSeguroAccountCredentials;
use PagSeguroServiceException;
use PagSeguroConfig;
use PagSeguroNotificationService;
use PagSeguroNotificationType;
use Exception;
use PagSeguroLibrary;
use common\models\stores\StorePaymentMethods;

/**
 * Class Pagseguro
 * @package sommerce\components\payments\methods
 */
class Pagseguro extends BasePayment
{
    /**
     * @var string - url action
     */
    public $action = null;

    public function __construct(array $config = [])
    {
        PagSeguroLibrary::init();

        parent::__construct($config);
    }

    /**
     * Checkout
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $email
     * @param StorePaymentMethods $details
     * @return array|mixed
     * @throws Exception
     */
    public function checkout($checkout, $store, $email, $details)
    {
        $paymentMethodOptions = $details->getOptions();

        $directPaymentRequest = new PagSeguroPaymentRequest();
        $directPaymentRequest->addItem($checkout->id, static::getDescription($checkout->id), 1, $checkout->price);
        $directPaymentRequest->setCurrency("BRL");
        $directPaymentRequest->setReference($checkout->id);

        $directPaymentRequest->setNotificationURL(SiteHelper::hostUrl() . '/pagseguro');
        $directPaymentRequest->setRedirectURL(SiteHelper::hostUrl() . '/cart');
        $credentials = new PagSeguroAccountCredentials(ArrayHelper::getValue($paymentMethodOptions, 'email'), ArrayHelper::getValue($paymentMethodOptions, 'token'));

        if (!empty(Yii::$app->params['testPagseguro'])) {
            PagSeguroConfig::setEnvironment('sandbox');
        }

        try {
            $redirectUrl = $directPaymentRequest->register($credentials);
        } catch (PagSeguroServiceException $e) {
            return static::returnError();
        }

        if (!empty($redirectUrl)) {
            return self::returnRedirect($redirectUrl);
        }

        return static::returnError();
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array|mixed
     * @throws Exception
     */
    public function processing($store)
    {
        $this->log(json_encode($_POST, JSON_PRETTY_PRINT));

        $notificationCode = ArrayHelper::getValue($_POST, 'notificationCode');
        $notificationType = ArrayHelper::getValue($_POST, 'notificationType');

        if (!$notificationCode || !$notificationType) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_PAGSEGURO);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();

        $credentials = new PagSeguroAccountCredentials(ArrayHelper::getValue($paymentMethodOptions, 'email'), ArrayHelper::getValue($paymentMethodOptions, 'token'));

        if (!empty(Yii::$app->params['testPagseguro'])) {
            PagSeguroConfig::setEnvironment('sandbox');
        }

        $notificationType = new PagSeguroNotificationType($notificationType);
        $strType = $notificationType->getTypeFromValue ();

        if ('TRANSACTION' !== $strType) {
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        try {
            $response = PagSeguroNotificationService::checkTransaction(
                $credentials,
                $notificationCode
            );
        } catch (PagSeguroServiceException $e) {
            die($e->getMessage());
        } catch (Exception $e) {
            die($e->getMessage());
        }

        $status = (int)$response->getStatus()->getValue();
        $amount = $response->getGrossAmount();
        $checkoutId = $response->getReference();

        if (empty($checkoutId)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $checkoutId,
                'method_id' => $paymentMethod->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no invoice
            return [
                'checkout_id' => $checkoutId,
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
                'checkout_id' => $this->_checkout->id,
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, [
            'DoExpressCheckoutPayment' => $response
        ]);

        if ($this->_checkout->price != $amount) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_checkout->method_status = $status;

        if (in_array($status, [
            1,
            2
        ])) {
            $this->_payment->status = Payments::STATUS_AWAITING;
        }

        if (7 == $status) {
            $this->_payment->status = Payments::STATUS_REFUNDED;
        }

        if ($status != 3) {
            return [
                'result' => 2,
                'content' => 'No final status'
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $notificationCode,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}