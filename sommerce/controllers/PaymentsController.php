<?php

namespace sommerce\controllers;

use common\models\store\Payments;
use common\models\stores\PaymentMethods;
use sommerce\components\payments\BasePayment;
use sommerce\components\payments\Payment;
use Yii;
use common\models\stores\Stores;
use yii\helpers\ArrayHelper;


/**
 * Class PaymentsController
 * @package sommerce\controllers
 */
class PaymentsController extends CustomController
{
    public $enableCsrfValidation = false;

    public $enableDomainValidation = false;

    /**
     * Process payment method
     * @param $method
     */
    public function actionResult($method)
    {

        /**
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        // if ($_SERVER['REMOTE_ADDR'] == '') {
        //     $json = json_decode('', 1);
        //     $_POST = ArrayHelper::getValue($json, 'POST', []);
        //     $_GET = ArrayHelper::getValue($json, 'GET', []);
        //     $_SERVER = ArrayHelper::getValue($json, 'SERVER', []);
        // }

        $paymentMethod = Payment::getPayment($method);
        $result = $paymentMethod->process($store);

        if (!empty($result['content']) && !$paymentMethod->redirectProcessing) {

            // Если успешная ошлата и есть контент - выводим его
            if (1 == ArrayHelper::getValue($result, 'result')) {
                return $result['content'];
            }

            // Показываем ошибки если включен режим отображения их, если нет то пустая страница
            if ($paymentMethod->showErrors) {
                return $result['content'];
            } else {
                return '';
            }
        }

        if (!in_array($method, [
            PaymentMethods::METHOD_AUTHORIZE,
            PaymentMethods::METHOD_STRIPE,
            PaymentMethods::METHOD_PAYPAL
        ])) {
            return $this->redirect('/cart');
        }

        $checkoutId = ArrayHelper::getValue($result, 'checkout_id');
        if (!$checkoutId) {
            $checkoutId = ArrayHelper::getValue($_GET, 'checkout_id');
        }
        return $this->render('payment_result.twig', [
            'payment_result' => $paymentMethod::getPaymentResult($checkoutId),
        ]);
    }
}