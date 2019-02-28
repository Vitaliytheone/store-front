<?php

namespace store\controllers;

use store\components\payments\Payment;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentsController
 * @package store\controllers
 */
class PaymentsController extends CustomController
{
    public $enableCsrfValidation = false;

    public $enableDomainValidation = false;

    /**
     * Process payment method
     * @param string $method
     * @return string|\yii\web\Response
     * @throws \yii\base\UnknownClassException
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

        if (!$paymentMethod->paymentResult) {
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