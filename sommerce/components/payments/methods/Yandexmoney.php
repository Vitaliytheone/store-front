<?php

namespace sommerce\components\payments\methods;

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
 * Class Yandexmoney
 * @package sommerce\components\payments\methods
 */
class Yandexmoney extends BasePayment
{
    /**
     * @var string - url action
     */
    public $action = 'https://money.yandex.ru/quickpay/confirm.xml';

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

        return static::returnForm($this->getFrom(), [
            'receiver' => ArrayHelper::getValue($paymentMethodOptions, 'wallet_number'),
            'label' => $checkout->id,
            'formcomment' => static::getDescription($checkout->id),
            'short-dest' => static::getDescription($checkout->id),
            'targets' => static::getDescription($checkout->id),
            'sum' => $checkout->price,
            'quickpay-form' => 'shop',
            'paymentType' => 'PC',
            'successURL' => SiteHelper::hostUrl() . '/cart'
        ]);
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $notificationType = ArrayHelper::getValue($_POST, 'notification_type'); // p2p-incoming / card-incoming - с кошелька / с карты
        $operationId = ArrayHelper::getValue($_POST, 'operation_id'); // Идентификатор операции в истории счета получателя.
        $amount = (ArrayHelper::getValue($_POST, 'amount')); // Сумма, которая зачислена на счет получателя.
        $withdrawAmount = (ArrayHelper::getValue($_POST, 'withdraw_amount')); // Сумма, которая списана со счета отправителя.
        $currency = ArrayHelper::getValue($_POST, 'currency'); // Код валюты — всегда 643 (рубль РФ согласно ISO 4217).
        $datetime = ArrayHelper::getValue($_POST, 'datetime'); // Дата и время совершения перевода.
        $sender = ArrayHelper::getValue($_POST, 'sender'); // Для переводов из кошелька — номер счета отправителя. Для переводов с произвольной карты — параметр содержит пустую строку.
        $coderpro = ArrayHelper::getValue($_POST, 'codepro'); // Для переводов из кошелька — перевод защищен кодом протекции. Для переводов с произвольной карты — всегда false.
        $label = intval(ArrayHelper::getValue($_POST, 'label')); // Метка платежа. Если ее нет, параметр содержит пустую строку.
        $sha1Hash = ArrayHelper::getValue($_POST, 'sha1_hash'); // SHA-1 hash параметров уведомления.

        if (empty($notificationType) || empty($operationId) || empty($amount) || empty($withdrawAmount) || empty($currency)
            || empty($datetime) || empty($coderpro) || empty($label) || empty($sha1Hash)) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        if ('card-incoming' != $notificationType && empty($sender)) {
            return [
                'result' => 2,
                'content' => 'no data'
            ];
        }

        $paymentMethod = $this->getStorePayMethod($store, PaymentMethods::METHOD_YANDEX_MONEY);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        if (empty($label)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $label,
                'method_id' => $paymentMethod->id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            // no invoice
            return [
                'checkout_id' => $label,
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
                'checkout_id' => $label,
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $_POST);

        $paymentMethodOptions = $paymentMethod->getOptions();

        $hash = sha1(implode('&', [
            $notificationType,
            $operationId,
            $amount,
            $currency,
            $datetime,
            $sender,
            $coderpro,
            ArrayHelper::getValue($paymentMethodOptions, 'secret_word'),
            $label
        ]));

        if ($hash != $sha1Hash) {
            return [
                'result' => 2,
                'content' => 'bad signature'
            ];
        }

        if ($this->_checkout->price != $amount && $this->_checkout->price != $withdrawAmount) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_payment->transaction_id = $operationId;
        $this->_payment->status = Payments::STATUS_AWAITING;

        return [
            'result' => 1,
            'transaction_id' => $operationId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}