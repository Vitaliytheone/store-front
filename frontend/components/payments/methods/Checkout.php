<?php
namespace frontend\components\payments\methods;

use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\store\PaymentsLog;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use Yii;
use frontend\components\payments\BasePayment;
use common\helpers\SiteHelper;
use yii\helpers\ArrayHelper;

/**
 * Class Checkout
 * @package app\components\payments\methods
 */
class Checkout extends BasePayment {

    public $action;
    public $method = 'GET';

    const TEST_MODE_ON = 1;
    const MODE_TEST_OFF = 0;

    const HOSTED_URL_SANDBOX = 'https://sandbox.2checkout.com/checkout/purchase';
    const HOSTED_URL_PRODUCTION = 'https://www.2checkout.com/checkout/purchase';

    public function __construct(array $config = [])
    {
        return parent::__construct($config);
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
        $mode = (int)ArrayHelper::getValue($paymentMethodOptions, 'test_mode', null);
        $accountNumber = ArrayHelper::getValue($paymentMethodOptions, 'account_number', null);
        $secretWord = ArrayHelper::getValue($paymentMethodOptions, 'secret_word', null);

        if (!isset($mode, $accountNumber, $secretWord)) {
            return static::returnError();
        }

        $amount = number_format($checkout->price, 2, '.', '');
        $receiptLinkUrl = SiteHelper::hostUrl() . '/twocheckout';

        $queryParams = http_build_query([
            'sid' => $accountNumber,
            'mode' => '2CO',
            'li_0_type' => 'product',
            'li_0_name' => static::getDescription($email),
            'li_0_quantity' => 1,
            'li_0_price' => $amount,
            'li_0_tangible' => 'N',
            'currency_code' => $store->currency,
            'purchase_step' => 'review-cart',
            'x_receipt_link_url' => $receiptLinkUrl,
            'merchant_order_id' => $checkout->id,
        ]);

        $queryUrl = $mode === self::TEST_MODE_ON ? self::HOSTED_URL_SANDBOX : self::HOSTED_URL_PRODUCTION;

        return static::returnRedirect($queryUrl . '?' . $queryParams);
    }

    /**
     * Processing payments result
     * @param Stores $store
     */
    public function processing($store)
    {
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_2CHECKOUT,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED
        ]);

        $paymentMethodOptions = $paymentMethod->getDetails();
        $secretWord = ArrayHelper::getValue($paymentMethodOptions, 'secret_word', null);

        if (empty($paymentMethod) && !isset($secretWord)) {
            // no invoice
            return [
                'result' => 2,
                'content' => 'bad payment method'
            ];
        }

        $paymentParams = [];
        if (!$this->checkPayment($secretWord, $paymentParams)) {
            return [
                'result' => 2,
                'content' => 'bad check payment result'
            ];
        }

//        return [
//            'result' => 1,
//            'transaction_id' => $transactionId,
//            'amount' => $this->_checkout->price,
//            'checkout_id' => $this->_checkout->id,
//        ];
    }


    /**
     * Check 2Checkout payment
     * @param $secretWord
     * @param $paymentParams
     * @return bool
     * @throws Co2Exception
     */
    public function checkPayment($secretWord, $paymentParams)
    {
        $requredFields = ['sid', 'total', 'order_number', 'key'];
        $isRequredFieldsExist = !array_diff_key(array_flip($requredFields), $paymentParams);
        if (!$isRequredFieldsExist) {
            throw new Co2Exception("Required payload fields does not exist!");
        }

        $hashSid = $paymentParams['sid'];
        $hashTotal = $paymentParams['total'];
        $hashOrder = $paymentParams['order_number'];
        $StringToHash = strtoupper(md5($secretWord . $hashSid . $hashOrder . $hashTotal));

        return $StringToHash != $paymentParams['key'];
    }

}