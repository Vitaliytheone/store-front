<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\stores\PaymentMethods;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\StorePaymentMethods;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\EditPaymentMethodForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PaymentsTrait
 * @property Controller $this
 * @package sommerce\modules\admin\controllers
 */
trait PaymentsTrait {

    /**
     * Settings payments. Payment methods list
     * @return string
     */
    public function actionPayments()
    {
        $this->view->title = Yii::t('admin', 'settings.payments_page_title');
        $this->addModule('adminPayments');

        $paymentMethods = StorePaymentMethods::findAll([
            'store_id' => yii::$app->store->getId(),
        ]);

        $availableMethod = PaymentMethodsCurrency::getSupportCurrency();
        Yii::debug($availableMethod); // TODO del

        return $this->render('payments', [
            'paymentMethods' => $paymentMethods,
            'availableMethod' => $availableMethod,
        ]);
    }

    /**
     * Settings payments. Payment method settings
     * @param $method integer method->id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionPaymentsSettings($method)
    {

        $request = yii::$app->getRequest();
        $storeId = yii::$app->store->getId();
        $methodName = PaymentMethods::getOneMethod($method);

        $this->view->title = Yii::t('admin', "settings.payments_edit_$methodName");

        $paymentModel = EditPaymentMethodForm::findOne([
            'store_id' => $storeId,
            'method_id' => $method,
        ]);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        $paymentModel->setUser(Yii::$app->user);

        Yii::debug($request->post(), 'POST'); // TODO del

        // FIXME не сохраняет ПОСТ из-за ошибки валидации
        if ($paymentModel->changeSettings($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_saved'));
            return $this->redirect(Url::toRoute(['/settings/payments']));
        }


        return $this->render('payments', [
            'method' => $method,
            'methodName' => $methodName,
            'paymentModel' => $paymentModel,
        ]);
    }

    /**
     * Settings payments. Toggle payment method active AJAX action.
     * @param $method integer method->id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionPaymentsToggleActive($method)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $storeId = yii::$app->store->getId();

        if (!$request->isAjax) {
            exit;
        }

        $active = $request->post('active', null);

        if (is_null($active)) {
            throw new BadRequestHttpException();
        }

        $paymentModel = EditPaymentMethodForm::findOne([
            'store_id' => $storeId,
            'method_id' => $method,
        ]);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        $paymentModel->setUser(Yii::$app->user);

        return [
            'active' => $paymentModel->setActive($active|0),
        ];
    }
}