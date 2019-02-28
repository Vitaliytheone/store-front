<?php

namespace superadmin\controllers;

use common\models\sommerces\Params;
use control_panel\components\ActiveForm;
use control_panel\components\SuperAccessControl;
use control_panel\helpers\Url;
use common\models\sommerces\Content;
use common\models\sommerces\NotificationEmail;
use common\models\sommerces\SuperAdmin;
use superadmin\models\forms\ChangeStaffPasswordForm;
use superadmin\models\forms\CreateNotificationEmailForm;
use superadmin\models\forms\CreateStaffForm;
use superadmin\models\forms\EditApplicationsForm;
use superadmin\models\forms\EditContentForm;
use superadmin\models\forms\EditNotificationEmailForm;
use superadmin\models\forms\EditPaymentForm;
use superadmin\models\forms\EditStaffForm;
use superadmin\models\search\ApplicationsSearch;
use superadmin\models\search\ContentSearch;
use superadmin\models\search\NotificationEmailSearch;
use superadmin\models\search\PaymentMethodsSearch;
use superadmin\models\search\StaffSearch;
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

    /**
     * @return array
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
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'edit-staff',
                    'create-staff',
                    'staff-password',
                    'edit-payment',
                    'edit-content',
                    'edit-application',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'edit-staff' => ['POST'],
                    'create-staff' => ['POST'],
                    'staff-password' => ['POST'],
                    'edit-payment' => ['POST', 'GET'],
                    'edit-content' => ['POST'],
                    'edit-application' => ['POST', 'GET'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'edit-staff',
                    'create-staff',
                    'staff-password',
                    'edit-payment',
                    'edit-content',
                    'edit-application',
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
     * @throws NotFoundHttpException
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
     * @return array
     * @throws NotFoundHttpException
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

    /**
     * Show services list
     * @return string
     */
    public function actionApplications(): string
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.applications');

        $applications = new ApplicationsSearch();

        return $this->render('applications', [
            'params' => $applications->search()
        ]);
    }

    /**
     * Edit applications (services) options
     *
     * @access public
     * @param string $code
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditApplication($code)
    {
        $params = Params::findOne([
            'category' => Params::CATEGORY_SERVICE,
            'code' => $code,
        ]);

        if (!$params) {
            throw new NotFoundHttpException();
        }

        $model = new EditApplicationsForm();
        $model->setParams($params);

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
            'content' => $this->renderPartial('layouts/_edit_applications_form', [
                'model' => $model,
                'params' => $params,
            ])
        ];
    }

}