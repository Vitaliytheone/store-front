<?php

namespace sommerce\controllers;

use common\models\stores\PaymentMethods;
use sommerce\components\payments\Payment;
use yii\base\UnknownClassException;
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
     * @return string|\yii\web\Response
     * @throws UnknownClassException
     */
    public function actionResult($method)
    {

        $paymentMethod = Payment::getPayment($method);
        $result = $paymentMethod->process($this->store);

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
            PaymentMethods::METHOD_STRIPE_3D_SECURE,
            PaymentMethods::METHOD_PAYPAL,
            PaymentMethods::METHOD_PAYPAL_STANDARD,
            PaymentMethods::METHOD_MERCADOPAGO,
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