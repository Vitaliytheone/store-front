<?php

namespace sommerce\components\payments\methods;

use Yii;
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
 * Class Billplz
 * @package sommerce\components\payments\methods
 */
class Billplz extends BasePayment {

    /**
     * @var string - url action
     */
    public $action = 'https://www.billplz.com/api/v3/bills';

    public $method = 'POST';

    public $redirectProcessing = true;

    public function __construct(array $config = [])
    {
        if (!empty(Yii::$app->params['testBillplz'])) {
            $this->action = 'https://billplz-staging.herokuapp.com/api/v3/bills';
        }

        return parent::__construct($config);
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

        $collectionId = ArrayHelper::getValue($paymentMethodOptions, 'collectionId');
        $secret = ArrayHelper::getValue($paymentMethodOptions, 'secret');

        $result = $this->request($secret, [
            'collection_id' => $collectionId,
            'email' => $checkout->id . '@' . SiteHelper::host(),
            'mobile' => '',
            'description' => static::getDescription($checkout->id),
            'name' => $email,
            'amount' => $checkout->price * 100, // A positive integer in the smallest currency unit (e.g 100 cents to charge RM 1.00)
            'callback_url' => SiteHelper::hostUrl() . '/billplz?checkoutId=' . $checkout->id,
            'redirect_url' => SiteHelper::hostUrl() . '/billplz?checkoutId=' . $checkout->id,
        ]);

        if (!empty($result)) {
            $bodyResult = json_decode($result, true);
            $url = ArrayHelper::getValue($bodyResult, 'url');

            if ($url) {
                return self::returnRedirect($url);
            }
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($checkout->id, $result);

        return self::returnError();
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $checkoutId = ArrayHelper::getValue($_GET, 'checkoutId');
        $billplz = ArrayHelper::getValue($_GET, 'billplz');

        if (!$checkoutId || !$billplz || empty($billplz['id'])) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_BILLPLZ);

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
                'content' => 'bad invoice payment'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();

        $collectionId = ArrayHelper::getValue($paymentMethodOptions, 'collectionId');
        $secret = ArrayHelper::getValue($paymentMethodOptions, 'secret');

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $_GET);

        $this->action = $this->action . '/' . $billplz['id'];

        $result = $this->request($secret);
        $result = json_decode($result, true);

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $result);

        if (empty($result) || !is_array($result)) {
            return [
                'result' => 2,
                'content' => 'bad payment'
            ];
        }

        $amount = ArrayHelper::getValue($result, 'amount');
        $resultCollectionId = ArrayHelper::getValue($result, 'collection_id');
        $paid = ArrayHelper::getValue($result, 'paid', false);
        $status = ArrayHelper::getValue($result, 'state');

        if ($resultCollectionId != $collectionId) {
            return [
                'result' => 2,
                'content' => 'bad payment'
            ];
        }

        if ($amount != ($this->_checkout->price * 100)) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_payment->transaction_id = $billplz['id'];
        $this->_payment->status = Payments::STATUS_AWAITING;
        $this->_payment->response_status = $status;

        if (!$paid || 'paid' != $status) {
            return [
                'result' => 2,
                'content' => 'No final status'
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $billplz['id'],
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }

    /**
     * Request method
     * @param string $secret
     * @param array $post
     * @return mixed
     */
    protected function request($secret, $post = [])
    {
        $ch = curl_init($this->action);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, $secret . ":");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if (!empty(PROXY_CONFIG['main']['ip'])) {
            curl_setopt($ch,  CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
            curl_setopt($ch,  CURLOPT_PROXY ,PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port']);
        }

        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}