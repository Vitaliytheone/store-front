<?php

namespace sommerce\controllers;

use common\models\sommerce\Pages;
use common\models\sommerce\Checkouts;
use sommerce\components\payments\Payment;
use sommerce\helpers\PaymentsModalHelper;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

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

        $checkoutId = ArrayHelper::getValue($result, 'checkout_id') ?? ArrayHelper::getValue($_GET, 'checkout_id');

        if ($checkoutId) {
            $redirectUrl = Checkouts::getRedirectUrl($checkoutId);
            if (Pages::existUrl($redirectUrl)) {
                return $this->redirect($redirectUrl);
            }
        }

        return $this->redirect(Url::home());
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

        $paymentsHelper = new PaymentsModalHelper();
        $paymentsHelper->setStore($this->store);
        $paymentsHelper->addModal(PaymentsModalHelper::SUCCESS_MODAL, $checkout);


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
        $paymentsHelper = new PaymentsModalHelper();
        $paymentsHelper->addModal(PaymentsModalHelper::FAILED_MODAL);
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