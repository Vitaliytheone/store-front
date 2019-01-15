<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\stores\PaymentMethods;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\StorePaymentMethods;
use my\components\ActiveForm;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\AddPaymentMethodForm;
use sommerce\modules\admin\models\forms\EditPaymentMethodForm;
use sommerce\modules\admin\models\forms\UpdatePositionsPaymentsForm;
use sommerce\modules\admin\models\search\PaymentsSettingsSearch;
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
trait PaymentsTrait
{
    /**
     * Settings payments. Payment methods list
     * @return string
     */
    public function actionPayments()
    {
        $this->view->title = Yii::t('admin', 'settings.payments_page_title');
        $this->addModule('adminPayments', [
            'action_update_pos' => Url::toRoute('/settings/update-payment-positions'),
        ]);

        $store = Yii::$app->store->getInstance();
        $paymentMethods = new PaymentsSettingsSearch();
        $paymentMethods->setStore($store);

        $availableMethods = PaymentMethodsCurrency::getSupportPaymentMethods($store);

        return $this->render('payments', [
            'paymentMethods' => $paymentMethods->search(),
            'availableMethods' => $availableMethods,
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
        $paymentModel = EditPaymentMethodForm::findOne($method);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        $methodName = PaymentMethods::getMethodName($paymentModel->method_id);
        $this->view->title = Yii::t('admin', "settings.payments_edit_$methodName");

        $paymentModel->setUser(Yii::$app->user);

        if ($paymentModel->changeSettings($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_saved'));
            return $this->redirect(Url::toRoute(['/settings/payments']));
        }

        $paymentMethod = PaymentMethods::findOne($paymentModel->method_id);
        if (!$paymentMethod) {
            throw new NotFoundHttpException();
        }

        return $this->render('payments', [
            'method' => $method,
            'methodName' => $methodName,
            'paymentModel' => $paymentModel,
            'paymentData' => [
                'icon' => $paymentMethod->icon,
                'description' => $paymentMethod->getSettingsFormDescription(),
            ],
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

        if (!$request->isAjax) {
            exit;
        }

        $active = $request->post('active', null);

        if (is_null($active)) {
            throw new BadRequestHttpException();
        }

        $paymentModel = EditPaymentMethodForm::findOne($method);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        $paymentModel->setUser(Yii::$app->user);

        return [
            'active' => $paymentModel->setActive($active|0),
        ];
    }

    /**
     * Add new payments to current Store Pay Method
     * @return array
     */
    public function actionAddPaymentMethod(): array
    {
        $storeId = Yii::$app->store->getId();

        $model = new AddPaymentMethodForm();
        $model->setStoreId($storeId);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }

    /**
     * Update Store Payments methods positions after drag&drop AJAX action
     * @return array
     * @throws BadRequestHttpException
     * @throws \Throwable
     */
    public function actionUpdatePaymentPositions(): array
    {
        $request = Yii::$app->getRequest();

        $model = new UpdatePositionsPaymentsForm();
        $model->setUser(Yii::$app->user);

        if (!$model->updatePositions($request->post())) {
            throw new BadRequestHttpException('Change in POST detected');
        }

        return [true];
    }

}