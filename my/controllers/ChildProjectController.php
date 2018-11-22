<?php

namespace my\controllers;

use my\components\ActiveForm;
use common\models\panels\Auth;
use common\models\panels\Content;
use common\models\panels\Customers;
use my\models\forms\CreateChildForm;
use my\models\forms\CreateStaffForm;
use my\models\forms\EditStaffForm;
use my\models\forms\SetStaffPasswordForm;
use common\models\panels\Orders;
use common\models\panels\ProjectAdmin;
use my\models\search\ChildPanelsSearch;
use Yii;
use common\models\panels\Project;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;

/**
 * Class ChildProjectController
 * @package my\controllers
 */
class ChildProjectController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            /**
                             * @var $customer Customers
                             */
                            $customer = Yii::$app->user->getIdentity();

                            if (!$customer || !$customer->can('child')) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
                    ],
                ],

            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'order-domain' => ['POST'],
                    'staff-passwd' => ['POST'],
                    'staff-edit' => ['POST'],
                    'staffcreate' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['order-domain', 'staff-passwd', 'staff-edit', 'staffcreate']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['order-domain', 'staff-passwd', 'staff-edit', 'staffcreate'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function getViewPath()
    {
        return Yii::getAlias('@my/views/child_project');
    }

    /**
     * View Panels list
     * @return string
     */
    public function actionPanels()
    {
        $this->view->title = Yii::t('app', 'pages.title.child_panels');

        $panelsSearch = new ChildPanelsSearch();
        $panelsSearch->setParams([
            'customer_id' => Yii::$app->user->identity->id
        ]);

        return $this->render('panels', [
            'panels' => $panelsSearch->search(),
            'note' => Content::getContent('child_panels'),
            'accesses' => [
                'canCreate' => Orders::can('create_child_panel', [
                    'customerId' => Yii::$app->user->identity->id
                ])
            ]
        ]);
    }

    /**
     * Create order
     * @return string|Response
     * @throws \Throwable
     */
    public function actionOrder()
    {
        /**
         * @var Auth $user
         */
        $user = Yii::$app->user->getIdentity();
        if (!Orders::can('create_child_panel', [
            'customerId' => $user->id
        ])) {
            return $this->redirect('/childpanels');
        }

        $this->view->title = Yii::t('app', 'pages.title.child_order');

        $model = new CreateChildForm();
        $model->setUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/invoices/' . $model->code);
        }

        return $this->render('order', [
            'model' => $model,
            'note' => Content::getContent('nameservers_child'),
            'user' => $user,
        ]);
    }

    /**
     * Domain order validation
     * @return array
     * @throws \Throwable
     */
    public function actionOrderDomain()
    {
        /**
         * @var $user Auth
         */
        $user = Yii::$app->user->getIdentity();

        $model = new CreateChildForm();
        $model->setUser($user);

        $model->scenario = CreateChildForm::SCENARIO_CREATE_DOMAIN;

        if ($user->can('domains') && $model->load(Yii::$app->request->post())) {
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
     * Panel staffs list
     * @param $id
     * @return string|Response
     * @throws \yii\base\ExitException
     */
    public function actionStaff($id)
    {
        $this->view->title = Yii::t('app', 'pages.title.child_staff');

        /**
         * @var Project $panel
         */
        $panel = $this->findModel($id);

        if (Project::STATUS_ACTIVE != $panel->act) {
            return $this->redirect('/child-panels');
        }

        $staffs = ProjectAdmin::find()->where(['pid' => $panel->id])->orderBy(['id'=>SORT_DESC])->all();

        return $this->render('staff', [
            'panel' => $panel,
            'staffs' => $staffs,
            'access' => [
                'canCreateStaff' => Project::hasAccess($panel, 'canCreateStaff')
            ]
        ]);
    }

    /**
     * Set staff password
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionStaffPasswd($id)
    {
        $staff = $this->findStaffModel($id);

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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionStaffEdit($id)
    {
        $staff = $this->findStaffModel($id);

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
     * @param $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws \yii\base\ExitException
     */
    public function actionStaffcreate($id)
    {
        $project = $this->findModel($id);

        if (!Project::hasAccess($project, 'canCreateStaff')) {
            throw new ForbiddenHttpException();
        }

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
     * Find model by id and class name
     * @param $id
     * @return Project|null|void
     * @throws \yii\base\ExitException
     */
    private function findModel($id)
    {
        $model = Project::findOne([
            'cid' => Yii::$app->user->identity->id,
            'id' => $id,
            'child_panel' => 1,
            'hide' => 0,
        ]);

        if (!$model || !Project::hasAccess($model, 'canEdit')) {
            $this->redirect('/');
            return Yii::$app->end();
        }

        return $model;
    }

    /**
     * Find model by id and class name
     * @param $id
     * @return ProjectAdmin|null
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    private function findStaffModel($id)
    {
        $model = ProjectAdmin::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $this->findModel($model->pid);

        return $model;
    }
}
