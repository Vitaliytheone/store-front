<?php
namespace sommerce\components\payments\methods;

use common\helpers\SiteHelper;
use Stripe\Stripe as StripeBase;
use Stripe\Error\Base as StripeError;
use Stripe\Charge as StripeCharge;
use Stripe\Error\ApiConnection as StripeException;
use Stripe\Webhook as StripeWebhook;
use Stripe\Error\SignatureVerification as StripeSignatureVerification;
use sommerce\components\payments\BasePayment;
use sommerce\helpers\AssetsHelper;
use common\models\stores\Stores;
use common\models\stores\PaymentMethods;
use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use yii\helpers\ArrayHelper;
use UnexpectedValueException;
use common\models\store\Carts;

/**
 * Class Stripe3dSecure
 * @package sommerce\components\payments\methods
 */
class Stripe3dSecure extends BasePayment {

    const DEFAULT_IMAGE = 'https://stripe.com/img/documentation/checkout/marketplace.png';
    /**
     * @var string - url action
     */
    public $action = 'https://checkout.stripe.com/checkout.js';
    public $redirectProcessing = true;

    /**
     * Checkout
     * @param Checkouts $checkout
     * @param Stores $store
     * @param string $user
     * @param PaymentMethods $details
     * @return array
     */
    public function checkout($checkout, $store, $user, $details)
    {
        $paymentMethodOptions = $details->getDetails();
        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');
        $options = $checkout->getUserDetails();

        StripeBase::setApiKey($secretKey);
        $email = ArrayHelper::getValue($options, 'email');
        $source = ArrayHelper::getValue($options, 'source');
        $clientSecret = ArrayHelper::getValue($options, 'client_secret');

        try {
            $charge = StripeCharge::create(array(
                'amount'   => $checkout->price * 100,
                'currency' => $store->currency,
                'source'  => $source,
                'metadata' => [
                    'payment_id' => $checkout->id,
                    'email' => $email
                ],
            ));
        } catch (StripeException $e) {
            // заносим запись в таблицу payments_log
            $log = new PaymentsLog();
            $log->log($checkout->id, $e->getMessage() . $e->getTraceAsString());
            $log->save(false);
            return static::returnError();
        } catch (StripeError $e) {
            // заносим запись в таблицу payments_log
            $log = new PaymentsLog();
            $log->log($checkout->id, $e->getMessage() . $e->getTraceAsString());
            $log->save(false);
            return static::returnError();
        }
        Carts::clearCheckoutItems($checkout);

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
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->save(false);
        return static::returnRedirect(SiteHelper::hostUrl() . '/'  . PaymentMethods::METHOD_STRIPE_3D_SECURE . '?checkout_id=' . $checkout->id);
    }

    /**
     * @param Stores $store
     * @param string $user
     * @param PaymentMethods $details
     * @return array
     */
    public function getJsEnvironments($store, $user, $details)
    {
        $paymentMethodOptions = $details->getDetails();
        $key = ArrayHelper::getValue($paymentMethodOptions, 'public_key');
        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');
        $image = ArrayHelper::getValue($paymentMethodOptions, 'image', static::DEFAULT_IMAGE);
        StripeBase::setApiKey($secretKey);

        AssetsHelper::addCustomScriptFile('https://checkout.stripe.com/checkout.js');
        AssetsHelper::addCustomScriptFile('https://js.stripe.com/v3/');

        return [
            'type' => $details->id,
            'return_url' => $store->getSite() . '/cart',
            'error_url' => $store->getSite() . '/cart',
            'configure' => [
                'key' => $key,
                'image' => $image,
                'locale' => 'auto'
            ],
            'open' => [
                'name' => $store->name,
            ]
        ];
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array|mixed
     */
    public function processing($store)
    {
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_STRIPE_3D_SECURE,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method',
            ];
        }

        $paymentMethodOptions = $paymentMethod->getDetails();
        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');
        $webhookSecretKey = ArrayHelper::getValue($paymentMethodOptions, 'webhook_secret');
        StripeBase::setApiKey($secretKey);

        $payload = file_get_contents("php://input");

        $sigHeader = ArrayHelper::getValue($_SERVER, "HTTP_STRIPE_SIGNATURE");

        $event = null;

        $this->log($payload);

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

        $this->log($event);

        $data = !empty($event['data']['object']) ? $event['data']['object'] : [];

        $transactionId = ArrayHelper::getValue($data, 'id');
        $currency = (string)ArrayHelper::getValue($data, 'currency');
        $status = (string)ArrayHelper::getValue($data, 'status');
        $metadata = ArrayHelper::getValue($data, 'metadata');
        $checkoutId = ArrayHelper::getValue($metadata, 'payment_id');

        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_STRIPE_3D_SECURE,
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

        if (!$this->_payment = Payments::findOne([
            'checkout_id' => $this->_checkout->id,
        ])) {
            return [
                'checkout_id' => $checkoutId,
                'result' => 2,
                'content' => 'payment not found'
            ];
        }

         if ($this->_payment->method != $this->_method) {
            // no invoice
            return [
                'checkout_id' => $checkoutId,
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        if (strtolower($currency) != strtolower($store->currency)) {
            // bad amount
            return [
                'result' => 2,
                'content' => 'bad currency'
            ];
        }

        $this->_payment->response_status = $status;
        $this->_payment->transaction_id = $transactionId;
        $this->_payment->status = Payments::STATUS_AWAITING;

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
            'fee' => 0,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        return [
            'method' => [
                'name' => 'method',
                'type' => 'hidden',
            ],
            'email' => [
                'name' => 'email',
                'type' => 'hidden',
            ],
            'client_secret' => [
                'name' => 'client_secret',
                'type' => 'hidden',
            ],
            'source' => [
                'name' => 'source',
                'type' => 'hidden',
            ],
        ];
    }
}