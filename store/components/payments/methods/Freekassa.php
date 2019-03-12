<?php

namespace store\components\payments\methods;

use store\components\payments\BasePayment;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use common\models\store\PaymentsLog;
use common\models\store\Payments;
use yii\helpers\ArrayHelper;
use common\models\stores\StorePaymentMethods;

/**
 * Class Freekassa
 * @package app\components\payments\methods
 *
 */
class Freekassa extends BasePayment
{

    /**
     * @var string - url action
     */
    public $action = 'https://www.free-kassa.ru/merchant/cash.php';

    public $method = 'GET';

    /**
     * Redirect to result page
     * @inheritdoc
     */
    public $paymentResult = false;

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

        $sign = md5(ArrayHelper::getValue($paymentMethodOptions, 'merchant_id') . ':' . $checkout->price . ':' . ArrayHelper::getValue($paymentMethodOptions, 'secret_word') . ':' . $checkout->id);

        return static::returnForm($this->getFrom(), [
            'm' => ArrayHelper::getValue($paymentMethodOptions, 'merchant_id'),
            'o' => $checkout->id,
            's' => $sign,
            'lang' => 'ru',
            'us_login' => $email,
            'oa' => $checkout->price
        ]);
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $amount = ArrayHelper::getValue($_POST, 'AMOUNT');
        $merchantId = ArrayHelper::getValue($_POST, 'MERCHANT_ID');
        $merchantOrderId = ArrayHelper::getValue($_POST, 'MERCHANT_ORDER_ID');
        $sign = ArrayHelper::getValue($_POST, 'SIGN');
        $intId = ArrayHelper::getValue($_POST, 'intid');

        if (empty($amount) || empty($merchantId) || empty($merchantOrderId) || empty($sign)) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_FREE_KASSA);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        if (empty($merchantOrderId)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $merchantOrderId,
                'method_id' => $paymentMethod->method_id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no invoice
            return [
                'checkout_id' => $merchantOrderId,
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
                'checkout_id' => $merchantOrderId,
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $_POST);

        $paymentMethodOptions = $paymentMethod->getOptions();

        $signature = [
            ArrayHelper::getValue($paymentMethodOptions, 'merchant_id'),
            $amount,
            ArrayHelper::getValue($paymentMethodOptions, 'secret_word2'),
            $merchantOrderId
        ];

        $signature = md5(implode(":", $signature));

        if ($sign != $signature) {
            return [
                'result' => 2,
                'content' => 'bad signature'
            ];
        }

        if ($this->_checkout->price != $amount) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_payment->transaction_id = $intId;
        $this->_payment->status = Payments::STATUS_AWAITING;

        return [
            'result' => 1,
            'transaction_id' => $intId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}