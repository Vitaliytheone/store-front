<?php

namespace sommerce\controllers;

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
     * @param string $method
     * @return string|\yii\web\Response
     * @throws UnknownClassException
     * @throws \yii\base\InvalidConfigException
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

    /**
     * Displays checkout page.
     *
     * @return string
     */
    public function actionCheckout()
    {
        return $this->renderPartial('checkout');
    }
}