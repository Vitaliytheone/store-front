<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\stores\Stores;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\helpers\PaymentsHelper;
use sommerce\modules\admin\models\forms\EditPaymentMethodForm;
use sommerce\modules\admin\models\search\PaymentMethodsSearch;
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

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        PaymentsHelper::updateStorePaymentMethods($store);

        $paymentMethods = PaymentMethodsSearch::findAll([
            'store_id' => yii::$app->store->getId(),
        ]);

        return $this->render('payments', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Settings payments. Payment method settings
     * @param $method
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionPaymentsSettings($method)
    {
        $request = yii::$app->getRequest();
        $storeId = yii::$app->store->getId();

        $this->view->title = Yii::t('admin', "settings.payments_edit_$method");

        $paymentModel = EditPaymentMethodForm::findOne([
            'store_id' => $storeId,
            'method' => $method,
        ]);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        $paymentModel->setUser(Yii::$app->user);

        if ($paymentModel->changeSettings($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_saved'));
            return $this->redirect(Url::toRoute(['/settings/payments']));
        }

        return $this->render('payments', [
            'method' => $method,
            'paymentModel' => $paymentModel,
        ]);
    }

    /**
     * Settings payments. Toggle payment method active AJAX action.
     * @param $method
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
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
            'method' => $method,
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