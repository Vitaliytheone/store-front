<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\Params;
use my\components\ActiveForm;
use my\components\SuperAccessControl;
use my\helpers\Url;
use common\models\panels\Content;
use common\models\panels\NotificationEmail;
use common\models\panels\PaymentGateway;
use common\models\panels\SuperAdmin;
use common\models\panels\Tariff;
use my\modules\superadmin\models\forms\ChangeStaffPasswordForm;
use my\modules\superadmin\models\forms\CreateNotificationEmailForm;
use my\modules\superadmin\models\forms\CreatePlanForm;
use my\modules\superadmin\models\forms\CreateStaffForm;
use my\modules\superadmin\models\forms\EditContentForm;
use my\modules\superadmin\models\forms\EditNotificationEmailForm;
use my\modules\superadmin\models\forms\EditPaymentForm;
use my\modules\superadmin\models\forms\EditPlanForm;
use my\modules\superadmin\models\forms\EditStaffForm;
use my\modules\superadmin\models\search\ContentSearch;
use my\modules\superadmin\models\search\NotificationEmailSearch;
use my\modules\superadmin\models\search\PaymentMethodsSearch;
use my\modules\superadmin\models\search\PlanSearch;
use my\modules\superadmin\models\search\StaffSearch;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Settings controller for the `superadmin` module
 */
class SettingsController extends CustomController
{
    public $activeTab = 'settings';


    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [SuperAdmin::CAN_WORK_WITH_SETTINGS],
                    ]
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'edit-staff',
                    'create-staff',
                    'staff-password',
                    'edit-payment',
                    'edit-plan',
                    'create-plan',
                    'edit-content',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'edit-staff' => ['POST'],
                    'create-staff' => ['POST'],
                    'staff-password' => ['POST'],
                    'edit-payment' => ['POST', 'GET'],
                    'edit-plan' => ['POST'],
                    'create-plan' => ['POST'],
                    'edit-content' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'edit-staff',
                    'create-staff',
                    'staff-password',
                    'edit-payment',
                    'edit-plan',
                    'create-plan',
                    'edit-content',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * List payments settings
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'settings.payments.title');

        $payments = new PaymentMethodsSearch();

        return $this->render('payments', [
            'payments' => $payments->search()
        ]);
    }

    /**
     * List staffs list
     * @return string
     */
    public function actionStaff()
    {
        $this->view->title = Yii::t('app/superadmin', 'settings.staff.title');

        $staffs = new StaffSearch();

        return $this->render('staff', [
            'staffs' => $staffs->search()
        ]);
    }

    /**
     * List emails list
     * @return string
     */
    public function actionEmail()
    {
        $this->view->title = Yii::t('app/superadmin', 'settings.email.title');

        $emails = new NotificationEmailSearch();

        return $this->render('email', [
            'emails' => $emails->search()
        ]);
    }

    /**
     * List plan list
     * @return string
     */
    public function actionPlan()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.plan');

        $plans = new PlanSearch();

        return $this->render('plan', [
            'plans' => $plans->search()
        ]);
    }

    /**
     * List content list
     * @return string
     */
    public function actionContent()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.content');

        $content = new ContentSearch();

        return $this->render('content', [
            'contents' => $content->search()
        ]);
    }

    /**
     * Edit staff data.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEditStaff($id)
    {
        if (!($superAdmin = SuperAdmin::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $model = new EditStaffForm();
        $model->setStaff($superAdmin);

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
     * Create staff.
     *
     * @access public
     * @return mixed
     */
    public function actionCreateStaff()
    {
        $model = new CreateStaffForm();

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
     * Change staff password action
     * @param int $id
     * @return array
     */
    public function actionStaffPassword($id)
    {
        $admin = SuperAdmin::findOne($id);

        if (!$admin) {
            throw new NotFoundHttpException();
        }

        $model = new ChangeStaffPasswordForm();
        $model->setAdmin($admin);

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
     * Get payment edit form or save data
     * @param $code
     */
    public function actionEditPayment($code)
    {
        $payment = $this->findPaymentMethod($code);

        $model = new EditPaymentForm();
        $model->setPayment($payment);

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'message' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success',
            ];
        }

        return [
            'content' => $this->renderPartial('layouts/_edit_payment_form', [
                'model' => $model,
                'payment' => $payment
            ])
        ];
    }

    /**
     * Create email.
     *
     * @access public
     * @return string
     */
    public function actionCreateEmail()
    {
        $this->view->title = 'Create email';

        $model = new CreateNotificationEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute('/settings/email'));
        }

        return $this->render('create_email', [
            'model' => $model
        ]);
    }

    /**
     * Edit email.
     *
     * @access public
     * @param int $id
     * @return string
     */
    public function actionEditEmail($id)
    {
        $this->view->title = 'Edit email';

        if (!($email = NotificationEmail::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $model = new EditNotificationEmailForm();
        $model->setEmail($email);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute('/settings/email'));
        }

        return $this->render('edit_email', [
            'model' => $model
        ]);
    }

    /**
     * Change email status enabled/disabled.
     *
     * @access public
     * @param int $id
     * @param int $status
     * @return string
     */
    public function actionEmailStatus($id, $status)
    {
        if (!($email = NotificationEmail::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $email->changeStatus($status);

        $this->redirect(Url::toRoute('/settings/email'));
    }

    /**
     * Edit plan data.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEditPlan($id)
    {
        if (!($tariff = Tariff::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $model = new EditPlanForm();
        $model->setTariff($tariff);

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
     * Create plan.
     *
     * @access public
     * @return mixed
     */
    public function actionCreatePlan()
    {
        $model = new CreatePlanForm();

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
     * Edit content data.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEditContent($id)
    {
        if (!($content = Content::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $model = new EditContentForm();
        $model->setContent($content);

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
     * Find payment method
     * @param $code
     * @return null|Params
     * @throws NotFoundHttpException
     */
    protected function findPaymentMethod($code)
    {
        $model = Params::findOne([
            'category' => Params::CATEGORY_PAYMENT,
            'code' => $code
        ]);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
