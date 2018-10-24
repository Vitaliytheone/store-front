<?php

namespace my\controllers;

use common\models\panels\Customers;
use my\components\ActiveForm;
use my\components\domains\Ahnames;
use my\helpers\DomainsHelper;
use common\models\panels\Auth;
use common\models\panels\Content;
use common\models\panels\DomainZones;
use my\models\forms\CreateOrderForm;
use my\models\forms\CreateStaffForm;
use my\models\forms\EditStaffForm;
use my\models\forms\SetStaffPasswordForm;
use common\models\panels\Orders;
use common\models\panels\ProjectAdmin;
use my\models\search\PanelsSearch;
use Yii;
use common\models\panels\Project;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;

/**
 * Class ProjectController
 * @package my\controllers
 */
class ProjectController extends CustomController
{

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

        $model = new CreateOrderForm();
        $model->setUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/invoices/' . $model->code);
        }

        return $this->render('order', [
            'model' => $model,
            'note' => Content::getContent('nameservers'),
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

        $model = new CreateOrderForm();
        $model->scenario = CreateOrderForm::SCENARIO_CREATE_DOMAIN;

        /**
         * @var $customer Customers
         */
        $customer = Yii::$app->user->getIdentity();

        // if ($customer->can('domains') && $model->load(Yii::$app->request->post())) { TODO:: Temporary allowed buy domain with panel for new customer
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
     * @return array
     */
    public function actionSearchDomains()
    {
        $domain = trim(Yii::$app->request->get('search_domain'));
        $zone = trim(Yii::$app->request->get('zone'));
        $zones = ArrayHelper::index(DomainZones::find()->all(), 'id');

        if (false !== strpos($domain, '.')) {
            $domain = explode(".", $domain)[0];
        }

        $domains = [
            $zone => ''
        ];

        foreach ($zones as $id => $zone) {
            $domains[$id] = mb_strtolower($domain . $zone->zone);
        }

        $result = Ahnames::domainsCheck(array_map([new DomainsHelper, 'idnToAscii'], $domains));
        $existsDomains = Orders::find()->andWhere([
            'domain' => array_keys($result),
            'item' => Orders::ITEM_BUY_DOMAIN,
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID,
                Orders::STATUS_ADDED,
                Orders::STATUS_ERROR
            ]
        ])->all();
        $existsDomains = ArrayHelper::getColumn($existsDomains, 'domain');

        $return = [];

        foreach ($domains as $id => $domain) {
            if (!isset($result[$domain])) {
                continue;
            }

            $return[] = [
                'zone' => $id,
                'domain' => $domain,
                'price' => $zones[$id]->price_register,
                'is_available' => $result[$domain] && !in_array($domain, $existsDomains)
            ];
        }

        return [
            'content' => $this->renderPartial('layouts/_search_domains_result', [
                'domains' => $return
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
