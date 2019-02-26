<?php

namespace sommerce\components\payments\methods;

use Yii;
use sommerce\components\payments\BasePayment;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use common\models\store\PaymentsLog;
use common\models\store\Payments;
use yii\helpers\ArrayHelper;
use common\models\stores\StorePaymentMethods;

/**
 * Class Paywant
 * @package sommerce\components\payments\methods
 */
class Paywant extends BasePayment
{
    /**
     * @var string - url action
     */
    public $action = null;

    /**
     * Redirect to result page
     * @inheritdoc
     */
    public $paymentResult = false;

    public function __construct(array $config = [])
    {
        $this->action = Yii::$app->params['store.paywant_proxy'];
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


        $hashOlustur = base64_encode(hash_hmac('sha256', implode("|", [
                $email,
                // $payment->id,
                $email,
                $checkout->id,
            ]).ArrayHelper::getValue($paymentMethodOptions, 'apiKey'), ArrayHelper::getValue($paymentMethodOptions, 'apiSecret'),true));

        $amount = $checkout->price + round((($checkout->price / 100) * ((float)ArrayHelper::getValue($paymentMethodOptions, 'fee', 0))), 2);

        $postData = array(
            'apiKey' => ArrayHelper::getValue($paymentMethodOptions, 'apiKey'),
            'hash' => $hashOlustur,
            'returnData' => $email,
            'userEmail' => $email,
            'userIPAddress' => $_SERVER['REMOTE_ADDR'],
            'userID' => $checkout->id,
            'proApi' => true,
            'productData' => array(
                'name' => static::getDescription($checkout->id),
                'amount' => ($amount * 100),
                'extraData' => $checkout->id
            )
        );
        //Метод работает без proxy напрямую
        $curl = curl_init();
        $curlOptions = array(
            CURLOPT_URL => $this->action,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($postData),
        );

        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            return static::returnError();
        } else {
            $jsonDecode = json_decode($response, false);
            if ($jsonDecode !== false) {
                if (!empty($jsonDecode->Status)) {
                    if ($jsonDecode->Status == 100) {
                        return self::returnRedirect(str_replace("http://", "https://", $jsonDecode->Message));
                    }
                }
            }
        }

        // заносим запись в таблицу payments_log
        PaymentsLog::log($checkout->id, $response);

        return static::returnError();
    }

    /**
     * Processing payments result
     * @param Stores $store
     * @return array
     */
    public function processing($store)
    {
        $siparisId = ArrayHelper::getValue($_POST, 'SiparisID', '');
        $extraData = ArrayHelper::getValue($_POST, 'ExtraData', '');
        $userId = ArrayHelper::getValue($_POST, 'UserID', '');
        $returnData = ArrayHelper::getValue($_POST, 'ReturnData', '');
        $status = ArrayHelper::getValue($_POST, 'Status', '');
        $odemeKanali = ArrayHelper::getValue($_POST, 'OdemeKanali', '');
        $odemeTutari = ArrayHelper::getValue($_POST, 'OdemeTutari', '');
        $netKazanc = ArrayHelper::getValue($_POST, 'NetKazanc', '');
        $urunTutari = ArrayHelper::getValue($_POST, 'UrunTutari', '');
        $hash = ArrayHelper::getValue($_POST, 'Hash', '');

        $paymentMethod = $this->getPaymentMethod($store, PaymentMethods::METHOD_PAYWANT);

        if (empty($paymentMethod)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        if (empty($extraData)
            || !($this->_checkout = Checkouts::findOne([
                'id' => $extraData,
                'method_id' => $paymentMethod->method_id
            ]))
            || in_array($this->_checkout->status, [Checkouts::STATUS_PAID])) {
            if (@$this->_checkout->status == 1) echo 'OK';
            // no invoice
            return [
                'checkout_id' => $extraData,
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
                'checkout_id' => $extraData,
                'result' => 2,
                'content' => 'bad invoice payment'
            ];
        }

        $paymentMethodOptions = $paymentMethod->getOptions();

        // заносим запись в таблицу payments_log
        PaymentsLog::log($this->_checkout->id, $_POST);

        $hashControl = [
            $siparisId,
            $extraData,
            $userId,
            $returnData,
            $status,
            $odemeKanali,
            $odemeTutari,
            $netKazanc . ArrayHelper::getValue($paymentMethodOptions, 'apiKey')
        ];
        $hashControl = base64_encode(hash_hmac('sha256', implode("|", $hashControl), ArrayHelper::getValue($paymentMethodOptions, 'apiSecret'), true));

        if ($hashControl !== $hash) {
            return [
                'result' => 2,
                'content' => 'Invalid hash'
            ];
        }

        $amount = $this->_checkout->price + round($this->_checkout->price / 100 * ArrayHelper::getValue($paymentMethodOptions, 'fee', 0), 2);

        if ($urunTutari != $amount) {
            return [
                'result' => 2,
                'content' => 'bad amount'
            ];
        }

        $this->_payment->transaction_id = $siparisId;
        $this->_payment->status = Payments::STATUS_AWAITING;

        if ($status != 100) {
            return [
                'result' => 2,
                'content' => 'No final status'
            ];
        }

        return [
            'result' => 1,
            'transaction_id' => $siparisId,
            'amount' => $this->_checkout->price,
            'checkout_id' => $this->_checkout->id,
            'content' => 'Ok'
        ];
    }
}