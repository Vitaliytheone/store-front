<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\PanelDomains;
use common\models\panels\SuperAdmin;
use common\models\panels\SuperAdminToken;
use my\components\ActiveForm;
use my\helpers\StringHelper;
use my\helpers\Url;
use common\models\panels\Project;
use my\modules\superadmin\models\forms\ChangeDomainForm;
use my\modules\superadmin\models\forms\DowngradePanelForm;
use my\modules\superadmin\models\forms\EditExpiryForm;
use my\modules\superadmin\models\forms\EditProjectForm;
use my\modules\superadmin\models\forms\EditProvidersForm;
use my\modules\superadmin\models\search\PanelsSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use my\components\SuperAccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;

/**
 * Account PanelsController for the `superadmin` module
 */
class PanelsController extends CustomController
{
    public $activeTab = 'panels';
    public $layout = 'superadmin_v2.php';

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
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'change-domain' => ['POST'],
                    'edit-expiry' => ['POST'],
                    'edit-providers' => ['POST'],
                    'edit' => ['POST'],
                    'generate-apikey' => ['GET'],
                    'downgrade' => ['POST'],
                    'change-status' => ['POST']
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
                    'edit'
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
     * Change panel domain.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * Change panel providers.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * Edit panel.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * Sign in as admin panel
     *
     * @access public
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSignInAsAdmin($id)
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

        return $this->redirect('http://' . $panelDomain->domain . '/admin/default/check?id=' . $token);
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

}
