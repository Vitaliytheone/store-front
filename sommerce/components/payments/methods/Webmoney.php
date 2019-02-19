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
 * Class Webmoney
 * @package sommerce\components\payments\methods
 */
class Webmoney extends BasePayment
{
    /**
     * @var string - url action
     */
    public $action = 'https://merchant.wmtransfer.com/lmi/payment.asp';

    /**
     * Redirect to result page
     * @inheritdoc
     */
    public $paymentResult = false;

    /**
     * @var string
     */
    public $charset = 'windows-1251';

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

        $code = $store->currency;

        if ('RUB' == $code) {
            $this->action = 'https://merchant.webmoney.ru/lmi/payment.asp';
        }

        return static::returnForm($this->getFrom(), [
            'LMI_PAYMENT_NO' => $checkout->id,
            'LMI_PAYMENT_AMOUNT' => $checkout->price,
            'LMI_PAYMENT_DESC' => static::getDescription($checkout->id),
            'LMI_PAYEE_PURSE' => ArrayHelper::getValue($paymentMethodOptions, 'purse'),
            'LMI_RESULT_URL' => SiteHelper::hostUrl($store->ssl) . '/webmoney',
            //'LMI_FAIL_URL' => SiteHelper::hostUrl($panel->ssl) . '/balance',
            //'LMI_SUCCESS_URL' => SiteHelper::hostUrl($panel->ssl) . '/balance',
            'id' => $checkout->id,
            'email' => $checkout->id . '@' . SiteHelper::host(),
        ]);
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_WEBMONEY);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();
        $payeePurse = ArrayHelper::getValue($_POST, 'LMI_PAYEE_PURSE', '');
        $id = ArrayHelper::getValue($_POST, 'id');
        $purse = ArrayHelper::getValue($paymentMethodOptions, 'purse');
        $secretKey = ArrayHelper::getValue($paymentMethodOptions, 'secret_key');

        if (!empty($_POST['LMI_PREREQUEST'])) {

            if(trim($payeePurse) != $purse) {
                $this->showErrors = true;
                return [
                    'result' => 2,
                    'content' => 'ERR: НЕВЕРНЫЙ КОШЕЛЕК ПОЛУЧАТЕЛЯ ' . $payeePurse
                ];
            } else {
                $this->showErrors = true;
                return [
                    'result' => 2,
                    'content' => 'YES'
                ];
            }
        }

        if (empty($id)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $id,
                'method_id' => $paymentMethod->method_id
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
        PaymentsLog::log($this->_checkout->id, $_POST);


        $commonString = [
            ArrayHelper::getValue($_POST, 'LMI_PAYEE_PURSE'),
            ArrayHelper::getValue($_POST, 'LMI_PAYMENT_AMOUNT'),
            ArrayHelper::getValue($_POST, 'LMI_PAYMENT_NO'),
            ArrayHelper::getValue($_POST, 'LMI_MODE'),
            ArrayHelper::getValue($_POST, 'LMI_SYS_INVS_NO'),
            ArrayHelper::getValue($_POST, 'LMI_SYS_TRANS_NO'),
            ArrayHelper::getValue($_POST, 'LMI_SYS_TRANS_DATE'),
            $secretKey,
            ArrayHelper::getValue($_POST, 'LMI_PAYER_PURSE'),
            ArrayHelper::getValue($_POST, 'LMI_PAYER_WM'),
        ];

        $commonString = implode("", $commonString);

        $signature = strtoupper(hash('sha256', $commonString));

        if ($signature != ArrayHelper::getValue($_POST, 'LMI_HASH')) {
            return [
                'result' => 2,
                'content' => 'bad signature'
            ];
        }

        if (strtolower(ArrayHelper::getValue($_POST, 'LMI_PAYEE_PURSE')) != strtolower($purse)) {
            return [
                'result' => 2,
                'content' => 'bad purse'
            ];
        }

        if ($this->_checkout->price != ArrayHelper::getValue($_POST, 'LMI_PAYMENT_AMOUNT')) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_payment->transaction_id = $payeePurse;
        $this->_payment->status = Payments::STATUS_AWAITING;
        
        return [
            'result' => 1,
            'transaction_id' => $payeePurse,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}