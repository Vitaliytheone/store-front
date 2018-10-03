<?php

namespace my\modules\superadmin\controllers;

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
use my\modules\superadmin\models\search\PaymentGatewaySearch;
use my\modules\superadmin\models\search\PlanSearch;
use my\modules\superadmin\models\search\StaffSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Settings controller for the `superadmin` module
 */
class SettingsController extends CustomController
{
    public $activeTab = 'settings';

    public $layout = 'superadmin_v2.php';

    /**
     * @inheritdoc
     */
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
        ];
    }

    /**
     * List payments settings
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'settings.payments.title');

        $payments = new PaymentGatewaySearch();

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
     * @throws NotFoundHttpException
     */
    public function actionEditStaff($id)
    {
        if (!($superAdmin = SuperAdmin::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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
        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * @throws NotFoundHttpException
     */
    public function actionStaffPassword($id)
    {
        $admin = SuperAdmin::findOne($id);

        if (!$admin) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEditPayment($id)
    {
        $payment = PaymentGateway::findOne($id);

        if (!$payment) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEditEmail($id)
    {
        if (!($email = NotificationEmail::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new EditNotificationEmailForm();
        $model->setEmail($email);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute('/settings/email'));
        }

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
     * Change email status enabled/disabled.
     *
     * @access public
     * @param int $id
     * @param int $status
     * @return string
     * @throws NotFoundHttpException
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
     * @throws NotFoundHttpException
     */
    public function actionEditPlan($id)
    {
        if (!($tariff = Tariff::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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
        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * @throws NotFoundHttpException
     */
    public function actionEditContent($id)
    {
        if (!($content = Content::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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
}
