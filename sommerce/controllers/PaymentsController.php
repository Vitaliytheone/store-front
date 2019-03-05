<?php

namespace sommerce\controllers;

use common\helpers\SiteHelper;
use common\models\sommerce\Pages;
use common\models\sommerce\Checkouts;
use sommerce\components\payments\Payment;
use sommerce\helpers\PaymentsModalHelper;
use Yii;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Cookie;

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


    /**
     * Displays success modal
     * @param int $checkoutId
     * @return string
     */
    public function actionSuccessPayment($checkoutId)
    {
        $checkout = $this->findCheckout($checkoutId);
        $cookies = Yii::$app->response->cookies;

        $paymentsHelper = new PaymentsModalHelper();
        $paymentsHelper->setStore($this->store);

        $cookies->add(new Cookie([
            'name' => 'modal',
            'value' => [
                'type' => 'payment_success',
                'data' => $paymentsHelper->getSuccessDetails($checkout)
            ]

        ]));

        if (Pages::existUrl($checkout->redirect_url)) {
            return $this->redirect($checkout->redirect_url);
        }

        return $this->redirect(Url::home());

    }

    /**
     * Displays success modal
     * @param int $checkoutId
     * @return string
     */
    public function actionFailPayment($checkoutId)
    {
        $checkout = $this->findCheckout($checkoutId);
        $cookies = Yii::$app->response->cookies;



        $cookies->add(new Cookie([
            'name' => 'modal',
            'value' => [
                'type' => 'payment_fail',
                'data' => []
            ]
        ]));

        if (Pages::existUrl($checkout->redirect_url)) {
            return $this->redirect($checkout->redirect_url);
        }

        return $this->redirect(Url::home());
    }

    /**
     * @param $checkoutId
     * @return Checkouts
     * @throws NotFoundHttpException
     */
    protected function findCheckout($checkoutId)
    {
        $checkout = Checkouts::findOne($checkoutId);

        if (!$checkout) {
            throw new NotFoundHttpException();
        }

        return $checkout;
    }
}