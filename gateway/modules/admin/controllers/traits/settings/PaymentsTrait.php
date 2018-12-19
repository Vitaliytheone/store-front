<?php
namespace admin\controllers\traits\settings;

use common\models\gateways\PaymentMethods;
use common\models\gateways\SitePaymentMethods;
use gateway\helpers\UiHelper;
use admin\components\Url;
use admin\models\forms\EditPaymentMethodForm;
use admin\models\search\PaymentMethodsSearch;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PaymentsTrait
 * @property Controller $this
 * @package admin\controllers
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

        $searchModel = new PaymentMethodsSearch();
        $searchModel->setGateway(Yii::$app->gateway->getInstance());

        return $this->render('payments', [
            'paymentMethods' => $searchModel->search(),
        ]);
    }

    /**
     * Settings payments. Payment method settings
     * @param integer $method
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionPaymentsSettings($method)
    {
        $this->view->title = Yii::t('admin', "settings.payments_edit_$method");

        $paymentMethod = $this->_findModel($method);

        $model = new EditPaymentMethodForm();
        $model->setGateway(Yii::$app->gateway->getInstance());
        $model->setPaymentMethod($paymentMethod);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_saved'));
            return $this->redirect(Url::toRoute(['/settings/payments']));
        }

        return $this->render('edit_payment', [
            'model' => $model,
        ]);
    }

    /**
     * Settings payments. Toggle payment method active AJAX action.
     * @param integer $method
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionPaymentsToggleActive($method)
    {
        $gateway = Yii::$app->gateway->getInstance();
        $paymentMethod = $this->_findModel($method);
        $attributes = [
            'method_id' => $paymentMethod->id,
            'site_id' => $gateway->id,
        ];

        if (!($sitePaymentMethods = SitePaymentMethods::findOne($attributes))) {
            $sitePaymentMethods = new SitePaymentMethods($attributes);
        }
        $sitePaymentMethods->visibility = !$sitePaymentMethods->visibility;
        $sitePaymentMethods->save(false);

        return [
            'active' => $sitePaymentMethods->visibility,
        ];
    }

    /**
     * @param int $id
     * @return null|PaymentMethods
     * @throws NotFoundHttpException
     */
    protected function _findModel($id)
    {
        if (empty($id) || !($model = PaymentMethods::findOne($id))) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}