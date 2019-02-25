<?php

namespace superadmin\controllers;

use common\models\panels\PanelDomains;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\SuperAdmin;
use common\models\panels\SuperAdminToken;
use control_panel\components\ActiveForm;
use control_panel\helpers\StringHelper;
use control_panel\helpers\Url;
use common\models\panels\Project;
use superadmin\models\forms\ChangeDomainForm;
use superadmin\models\forms\DowngradePanelForm;
use superadmin\models\forms\EditExpiryForm;
use superadmin\models\forms\EditProjectForm;
use superadmin\models\forms\EditProvidersForm;
use superadmin\models\search\PanelsSearch;
use superadmin\models\forms\EditPanelPaymentMethodsForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use control_panel\components\SuperAccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;

/**
 * Account PanelsController for the `superadmin` module
 */
class PanelsController extends CustomController
{
    public $activeTab = 'panels';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'change-domain',
                    'edit-expiry',
                    'edit-providers',
                    'edit',
                    'generate-apikey',
                    'downgrade',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'change-domain' => ['POST'],
                    'edit-expiry' => ['POST'],
                    'edit-providers' => ['POST'],
                    'edit' => ['POST'],
                    'generate-apikey' => ['GET'],
                    'downgrade' => ['POST'],
                    'change-status' => ['POST'],
                    'edit-payment-methods' => ['GET', 'POST'],
                    'delete-payment-method' => ['GET'],
                    'allow-payment' => ['GET'],
                    'allow-payment-with-same' => ['GET'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'change-domain',
                    'edit-expiry',
                    'edit-providers',
                    'generate-apikey',
                    'providers',
                    'downgrade',
                    'edit',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.panels');
        $panelsSearch = new PanelsSearch();
        $panelsSearch->setParams(Yii::$app->request->get());

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');
        $pageSize = Yii::$app->request->get('page_size');

        return $this->render('index', [
            'panels' => $panelsSearch->search(),
            'pageSizes' => PanelsSearch::getPageSizes(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'plans' => $panelsSearch->getAggregatedPlans(),
            'filters' => $filters,
            'pageSize' => $pageSize
        ]);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionChangeStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        $project = Project::findOne($id);
        $project->changeStatus($status);
        $this->redirect(Url::toRoute('/'. $this->activeTab));
    }

    /**
     * Change panel domain
     *
     * @access public
     * @param $id
     * @return array
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionChangeDomain($id)
    {
        $project = Project::findOne($id);
        $model = new ChangeDomainForm();
        $model->setProject($project);

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
     * Change panel expired.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditExpiry($id)
    {
        $project = $this->findModel($id);
        $model = new EditExpiryForm();
        $model->setProject($project);

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
     * Change panel providers
     *
     * @access public
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionEditProviders($id)
    {
        $project = $this->findModel($id);
        $model = new EditProvidersForm();
        $model->setProject($project);

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
     * Edit panel
     * @access public
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionEdit($id)
    {
        $project = $this->findModel($id);
        $model = new EditProjectForm();
        $model->setProject($project);

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
     * Generate uniq project apikey
     * @return array
     */
    public function actionGenerateApikey()
    {
        $key = StringHelper::randomString(64, 'abcdefghijklmnopqrstuwxyz0123456789');

        do {
            if (!Project::find()->andWhere([
                'apikey' => $key
            ])->exists()) {
                return [
                    'key' => $key
                ];
            }
        } while(true);
    }

    /**
     * Get providers
     * @param integer $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionProviders($id)
    {
        $project = $this->findModel($id);
        $model = new DowngradePanelForm();
        $model->setProject($project);

        return [
            'providers' => $model->getProviders()
        ];
    }

    /**
     * Downgrade panel
     *
     * @access public
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDowngrade($id)
    {
        $project = $this->findModel($id);
        $model = new DowngradePanelForm();
        $model->setProject($project);

        if (!$model->load(Yii::$app->request->post())) {
            $model->validate();
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }

        if ($model->save()) {
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
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function actionEditPaymentMethods($id)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.panels.edit_payment_methods');
        $project = $this->findModel($id);

        $model = new EditPanelPaymentMethodsForm();
        $model->setPanel($project);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->save();

            return $this->redirect(Url::toRoute(['panels/edit-payment-methods', 'id' => $id]));
        }

        return $this->render('edit_payment_methods', [
            'payments' => $model->getPaymentMethods(),
            'panel' => $project,
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @param $method_id
     * @param $same_method_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionAllowPaymentWithSame($id, $method_id, $same_method_id)
    {
        $project = $this->findModel($id);

        $model = new EditPanelPaymentMethodsForm();
        $model->setPanel($project);
        $model->allowPaymentMethodWithSame($method_id, $same_method_id);

        return $this->redirect(Url::toRoute(['panels/edit-payment-methods', 'id' => $id]));
    }

    /**
     * @param int $id - panel id
     * @param int $method_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeletePaymentMethod($id, $method_id)
    {
        $project = $this->findModel($id);

        $model = new EditPanelPaymentMethodsForm();
        $model->setPanel($project);
        $model->deletePaymentMethod($method_id);

        return $this->redirect(Url::toRoute(['panels/edit-payment-methods', 'id' => $id]));
    }

    /**
     * @param int $id - panel id
     * @param int $method_id
     * @param int $allow
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionAllowPayment($id, $method_id, $allow)
    {
        $project = $this->findModel($id);

        $model = new EditPanelPaymentMethodsForm();
        $model->setPanel($project);

        if (!$model->changeAvailability($method_id, $allow)) {
            throw new BadRequestHttpException();
        }

        return $this->redirect(Url::toRoute(['panels/edit-payment-methods', 'id' => $id]));
    }

    /**
     * Sign in as admin panel
     *
     * @access public
     * @param int $id
     * @param string $redirect link to redirect
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSignInAsAdmin($id, $redirect = null)
    {
        $project = $this->findModel($id);
        if (!($panelDomain = PanelDomains::find()->andWhere([
            'panel_id' => $project->id,
            'type' => PanelDomains::TYPE_SUBDOMAIN
        ])->andFilterWhere([
            'AND',
            ['like', 'domain', '.' . Yii::$app->params['panelDomain']],
        ])->one())) {
            throw new NotFoundHttpException();
        }

        /**
         * @var SuperAdmin $superUser
         */
        $superUser = Yii::$app->superadmin->getIdentity();
        $token = SuperAdminToken::getToken($superUser->id, SuperAdminToken::ITEM_PANELS, $project->id);
        $redirect = isset($redirect) ? '&redirect=' . urlencode($redirect) : '';

        return $this->redirect('http://' . $panelDomain->domain . '/admin/default/check?id=' . $token . $redirect);
    }


    /**
     * @param $id
     * @return null|Project
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $project = Project::findOne($id);

        if (!$project) {
            throw new NotFoundHttpException();
        }
        return $project;
    }

    /**
     * @param $id
     * @return null|PanelPaymentMethods
     * @throws NotFoundHttpException
     */
    protected function findPaymentMethodModel($id)
    {
        $paymentMethod = PanelPaymentMethods::findOne($id);

        if (!$paymentMethod) {
            throw new NotFoundHttpException();
        }
        return $paymentMethod;
    }
}
