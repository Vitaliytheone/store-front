<?php
namespace payments\methods;

use common\models\gateways\PaymentMethods;
use Yii;
use Stripe\Stripe as StripeBase;
use Stripe\Error\Base as StripeError;
use Stripe\Charge as StripeCharge;
use Stripe\Error\ApiConnection as StripeException;
use Stripe\Webhook as StripeWebhook;
use Stripe\Error\SignatureVerification as StripeSignatureVerification;
use payments\BasePayment;
use common\models\gateway\Payments;
use yii\helpers\ArrayHelper;
use UnexpectedValueException;

/**
 * Class Stripe
 * @package payments\methods
 */
class Stripe extends BasePayment {

    protected $_method_id = PaymentMethods::METHOD_STRIPE;

    const DEFAULT_IMAGE = 'https://stripe.com/img/documentation/checkout/marketplace.png';

    /**
     * @var string - url action
     */
    public $action = 'https://checkout.stripe.com/checkout.js';

    /**
     * @inheritdoc
     */
    public function checkouting()
    {
        $paymentMethodOptions = $this->getPaymentMethod()['options'];
        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');
        $options = $this->getPayment()->getUserDetails();

        StripeBase::setApiKey($secretKey);

        $email = ArrayHelper::getValue($options, 'email');
        $token = ArrayHelper::getValue($options, 'token');

        try {
            $charge = StripeCharge::create(array(
                'amount'   => $this->getPayment()->amount * 100,
                'currency' => $this->getPayment()->currency,
                'description' => $this->getDescription(),
                'source'  => $token,
                "metadata" => [
                    'payment_id' => $this->getPayment()->id,
                    'email' => $email
                ],
            ));
        } catch (StripeException $e) {
            // заносим запись в таблицу payments_log
            $this->dbLog($this->getPayment(), $e->getMessage() . $e->getTraceAsString());

            return static::returnError();
        } catch (StripeError $e) {
            // заносим запись в таблицу payments_log
            $this->dbLog($this->getPayment(), $e->getMessage() . $e->getTraceAsString());

            return static::returnError();
        }

        return static::returnRedirect($this->getReturnUrl());
    }

    /**
     * @inheritdoc
     */
    public function getJsEnvironments()
    {
        $paymentMethodOptions = $this->getPaymentMethod()['options'];
        $key = ArrayHelper::getValue($paymentMethodOptions, 'public_key');
        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');
        $image = ArrayHelper::getValue($paymentMethodOptions, 'image', static::DEFAULT_IMAGE);

        if (empty($secretKey)) {
            return parent::getJsEnvironments();
        }

        StripeBase::setApiKey($secretKey);

        return [
            'code' => 'stripe',
            'type' => $this->_method_id,
            'configure' => [
                'key' => $key,
                'image' => $image,
                'locale' => 'auto'
            ],
            'open' => [
                'name' => $this->getGateway()->domain,
            ],
            'payment_request' => [
                'country' => 'US',
                'currency' => strtolower($this->getPayment()->currency),
                'requestPayerName' => true,
                'requestPayerEmail' => true,
                'total' => [
                    'amount' => $this->getPayment()->amount * 100,
                    'label' => $this->getDescription(),
                ],
            ],
            'payment_request_button' => [
                'height' => '31px',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function processing()
    {
        $paymentMethodOptions = $this->getPaymentMethod()['options'];

        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');
        $webhookSecretKey = ArrayHelper::getValue($paymentMethodOptions, 'webhook_secret');

        StripeBase::setApiKey($secretKey);

        $payload = @file_get_contents("php://input");
        $sigHeader = ArrayHelper::getValue($_SERVER, "HTTP_STRIPE_SIGNATURE");
        $event = null;

        $this->fileLog($payload);

        if (empty($sigHeader)) {
            return [
                'result' => 2,
                'content' => 'bad data'
            ];
        }

        try {
            $event = StripeWebhook::constructEvent(
                $payload, $sigHeader, $webhookSecretKey
            );
        } catch(UnexpectedValueException $e) {
            return [
                'result' => 2,
                'content' => 'bad data'
            ];
        } catch(StripeSignatureVerification $e) {
            return [
                'result' => 2,
                'content' => 'bad signature'
            ];
        }

        $eventType = ArrayHelper::getValue($event, 'type');

        if ('charge.succeeded' != $eventType) {
            return [
                'result' => 2,
                'content' => 'bad event'
            ];
        }

        $this->fileLog($event);

        $data = !empty($event['data']['object']) ? $event['data']['object'] : [];
        $transactionId = ArrayHelper::getValue($data, 'id');
        $amount = ArrayHelper::getValue($data, 'amount', 0) / 100;
        $currency = (string)ArrayHelper::getValue($data, 'currency');
        $status = (string)ArrayHelper::getValue($data, 'status');
        $metadata = ArrayHelper::getValue($data, 'metadata');
        $paymentId = ArrayHelper::getValue($metadata, 'payment_id');

        $this->getPaymentById($paymentId);

        // заносим запись в таблицу payments_log
        $this->dbLog($this->_payment, $event);

        $this->getPayment()->response_status = $status;
        $this->getPayment()->transaction_id = $transactionId;
        $this->getPayment()->status = Payments::STATUS_PENDING;

        if ($amount != $this->getPayment()->amount) { // сверяем сумму оплаты payments.amount
            // bad amount
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        if (strtolower($currency) != strtolower($this->getPayment()->currency)) {
            // bad amount
            return [
                'result' => 2,
                'content' => 'bad currency'
            ];
        }

        if ($status != 'succeeded') {
            // no final status
            return [
                'result' => 2,
                'content' => 'no final status'
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $transactionId,
            'amount' => $this->getPayment()->amount,
            'payment_id' => $this->getPayment()->id,
            'content' => 'Ok'
        ];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return [
            "token" => [
                "name" => "token",
                "type" => "hidden"
            ],
            "email" => [
                "name" => "email",
                "type" => "hidden"
            ]
        ];
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return [
            $this->action
        ];
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validateUserDetails($data)
    {
        if (empty($data['token']) || empty($data['email']) || !is_string($data['token']) || !is_string($data['email'])) {
            return false;
        }

        return true;
    }
}