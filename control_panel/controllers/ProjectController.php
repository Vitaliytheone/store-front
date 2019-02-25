<?php

namespace control_panel\controllers;

use common\models\panels\Customers;
use control_panel\components\ActiveForm;
use common\models\panels\Auth;
use common\models\panels\Content;
use control_panel\models\forms\OrderPanelForm;
use control_panel\models\forms\CreateStaffForm;
use control_panel\models\forms\EditStaffForm;
use control_panel\models\forms\SetStaffPasswordForm;
use common\models\panels\Orders;
use common\models\panels\ProjectAdmin;
use control_panel\models\search\DomainsAvailableSearch;
use control_panel\models\search\PanelsSearch;
use Yii;
use common\models\panels\Project;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;

/**
 * Class ProjectController
 * @package control_panel\controllers
 */
class ProjectController extends CustomController
{
    public $activeTab = 'panels';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'order-domain' => ['POST'],
                    'staffpasswd' => ['POST'],
                    'staffedit' => ['POST'],
                    'staffcreate' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['order-domain', 'staffpasswd', 'staffedit', 'staffcreate', 'search-domains']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['order-domain', 'staffpasswd', 'staffedit', 'staffcreate', 'search-domains'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }

    /**
     * Panel staffs list
     * @param $id
     * @return string|Response
     * @throws \Throwable
     */
    public function actionStaff($id)
    {
        $this->view->title = Yii::t('app', 'pages.title.staff');

        /**
         * @var Project $panel
         */
        $panel = $this->findModel($id);

        if (Project::STATUS_ACTIVE != $panel->act) {
            return $this->redirect('/panels');
        }

        $staffs = ProjectAdmin::find()->where(['pid' => $panel->id])->orderBy(['id'=>SORT_DESC])->all();

        return $this->render('staff', [
            'panel' => $panel,
            'staffs' => $staffs
        ]);
    }

    /**
     * Create order
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionOrder()
    {
        /**
         * @var Auth $user
         */
        $user = Yii::$app->user->getIdentity();
        if (!Orders::can('create_panel', [
            'customerId' => $user->id
        ])) {
            return $this->redirect('/panels');
        }

        $this->view->title = Yii::t('app', 'pages.title.order');

        $model = new OrderPanelForm();
        $model->setUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/invoices/' . $model->code);
        }

        return $this->render('order', [
            'model' => $model,
            'note' => Content::getContent('nameservers'),
            'subdomainNote' => Content::getContent('subdomain_nameservers'),
            'user' => $user,
        ]);
    }

    /**
     * Create order
     * @return array|string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionOrderDomain()
    {
        $this->view->title = Yii::t('app', 'pages.title.order');

        $model = new OrderPanelForm();
        $model->scenario = OrderPanelForm::SCENARIO_CREATE_DOMAIN;

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }
            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => 'Invalid form data'
        ];
    }

    /**
     * View Panels list
     * @return string
     */
    public function actionPanels()
    {
        $this->view->title = Yii::t('app', 'pages.title.panels');

        $panelsSearch = new PanelsSearch();
        $panelsSearch->setParams([
            'customer_id' => Yii::$app->user->identity->id
        ]);

        return $this->render('panels', [
            'panels' => $panelsSearch->search(),
            'accesses' => [
                'canCreate' => Orders::can('create_panel', [
                    'customerId' => Yii::$app->user->identity->id
                ])
            ]
        ]);
    }

    /**
     * Set staff password
     * @param int $id
     * @return array|string|Response
     * @throws \Throwable
     */
    public function actionStaffpasswd($id)
    {
        $staff = ProjectAdmin::findOne($id);

        if (!$staff) {
            throw new NotFoundHttpException();
        }

        $this->findModel($staff->pid);

        $model = new SetStaffPasswordForm();
        $model->setStaff($staff);

        if ($model->load(Yii::$app->request->post())) {

            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => Yii::t('app', 'error.staff.can_not_change_password')
        ];
    }

    /**
     * Edit staff
     * @param int $id
     * @return array|string|Response
     * @throws \Throwable
     */
    public function actionStaffedit($id)
    {
        $staff = ProjectAdmin::findOne($id);

        if (!$staff) {
            throw new NotFoundHttpException();
        }

        $this->findModel($staff->pid);

        $model = new EditStaffForm();
        $model->setStaff($staff);

        if ($model->load(Yii::$app->request->post())) {

            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => Yii::t('app', 'error.staff.can_not_edit')
        ];
    }

    /**
     * Create project staff
     * @param int $id
     * @return array
     * @throws \Throwable
     */
    public function actionStaffcreate($id)
    {
        $project = $this->findModel($id);

        $model = new CreateStaffForm();
        $model->setProject($project);

        if ($model->load(Yii::$app->request->post())) {

            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => Yii::t('app', 'error.staff.can_not_create')
        ];
    }

    /**
     * Search available domains
     * @param string $search_domain
     * @param string $zone
     * @return array
     * @throws yii\base\UnknownClassException
     */
    public function actionSearchDomains(string $search_domain, string $zone): array
    {
        $domain = trim($search_domain);
        $zone = trim($zone);

        $domainsSearch = new DomainsAvailableSearch();

        return [
            'content' => $this->renderPartial('layouts/_search_domains_result', [
                'domains' => $domainsSearch->searchDomains($domain, $zone)
            ])
        ];
    }

    /**
     * Find model by id and class name
     * @param int $id
     * @return Response|Project
     * @throws \Throwable
     */
    private function findModel($id)
    {
        $model = Project::findOne([
            'child_panel' => 0,
            'cid' => Yii::$app->user->identity->id,
            'id' => $id
        ]);

        if (!$model || !Project::hasAccess($model, 'canEdit')) {
            $this->redirect('/');
            return Yii::$app->end();
        }

        return $model;
    }
}
