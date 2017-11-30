<?php

namespace frontend\modules\admin\controllers;

use common\components\ActiveForm;
use frontend\helpers\UiHelper;
use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\CreateProviderForm;
use frontend\modules\admin\models\forms\EditStoreSettingsForm;
use frontend\modules\admin\models\forms\ProvidersListForm;
use frontend\modules\admin\models\search\ProvidersSearch;
use frontend\modules\admin\models\forms\EditPaymentMethodForm;
use frontend\modules\admin\models\search\PaymentMethodsSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;


/**
 * Settings controller for the `admin` module
 */
class SettingsController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Add custom JS modules
        $this->addModule('settings');
        return parent::beforeAction($action);
    }

    /**
     * Settings general
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();

        $this->view->title = Yii::t('admin', 'settings.page_title');

        /** @var \common\models\stores\Stores $store */
        $store = Yii::$app->store->getInstance();
        $storeForm = EditStoreSettingsForm::findOne($store->id);

        if ($storeForm->updateSettings($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_updated'));
            return $this->refresh();
        }

        return $this->render('index', [
            'store' => $storeForm,
            'timezones' => Yii::$app->params['timezone'],
        ]);
    }

    /**
     * Settings providers
     * @return string
     */
    public function actionProviders()
    {
        $this->view->title = 'Settings providers';
        
        $search = new ProvidersSearch();

        $this->addModule('adminProviders');

        $model = new ProvidersListForm();
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_provider_updated'));
            return $this->refresh();
        }

        return $this->render('providers', [
            'providers' => $search->search()
        ]);
    }

    /**
     * Settings payments. Payment methods list
     * @return string
     */
    public function actionPayments()
    {
        $this->view->title = Yii::t('admin', 'settings.payments_page_title');

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

        if ($paymentModel->load($request->post()) && $paymentModel->save()) {
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

        $paymentModel->setAttribute('active', $active|0);
        $paymentModel->save();

        return [
            'active' => $paymentModel->active,
        ];
    }

    /**
     * Settings themes
     * @return string
     */
    public function actionThemes()
    {
        return $this->render('themes');
    }

    /**
     * Settings pages
     * @return string
     */
    public function actionPages()
    {
        return $this->render('pages');
    }

    /**
     * Settings blocks
     * @return string
     */
    public function actionBlocks()
    {
        return $this->render('blocks');
    }

    /**
     * Settings navigations
     * @return string
     */
    public function actionNavigations()
    {
        return $this->render('navigations');
    }

    /**
     * Create provider
     *
     * @access public
     * @return mixed
     */
    public function actionCreateProvider()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CreateProviderForm();
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_provider_created'));
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
}
