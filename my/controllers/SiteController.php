<?php

namespace my\controllers;

use my\components\ActiveForm;
use my\components\bitcoin\Bitcoin;
use my\components\Paypal;
use my\helpers\CurlHelper;
use common\models\panels\Content;
use my\models\forms\ChangeEmailForm;
use my\models\forms\ChangePasswordForm;
use my\models\forms\CreateMessageForm;
use my\models\forms\CreateTicketForm;
use my\models\forms\ResetPasswordForm;
use my\models\forms\RestoreForm;
use my\models\forms\SettingsForm;
use my\models\forms\SignupForm;
use my\models\search\InvoicesSearch;
use my\models\search\TicketsSearch;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\panels\Customers;
use my\models\forms\LoginForm;
use common\models\panels\Invoices;
use common\models\panels\PaymentGateway;
use common\models\panels\Payments;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class SiteController
 * @package my\controllers
 */
class SiteController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['signin', 'signup', 'restore', 'reset', 'checkout', 'invoice', 'error', 'redirect'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Before action
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action) {
        if (in_array($this->action->id, [
            'checkout',
            'invoices',
            'invoice',
        ])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Index
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        if ($this->hasActiveInvoice()) {
            $this->redirect('/invoices');
        } else {
            $this->redirect('/panels');
        }
    }

    /**
     * Create ticket message
     * @param int $id
     * @return array
     */
    public function actionMessage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ticket = $this->findModel($id, 'Tickets');
        $customer = Customers::findOne(Yii::$app->user->identity->id);

        $model = new CreateMessageForm();
        $model->setCustomer($customer);
        $model->setTicket($ticket);

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
            'error' => Yii::t('app', 'error.ticket.can_not_create_message')
        ];
    }

    /**
     * Show ticket details with messages
     * @param int $id
     * @param bool $clear
     * @return string
     */
    public function actionTicket($id, $clear = false)
    {
        $ticket = $this->findModel($id, 'Tickets');
        $ticket->makeReaded();

        $ticketMessages = TicketMessages::find()->where([
            'tid' => $ticket->id
        ])->joinWith(['customer', 'admin'])->orderBy(['date' => SORT_ASC])->all();

        return $this->renderPartial('ticket', [
            'ticketMessages' => $ticketMessages,
            'ticket' => $ticket,
            'showForm' => !$clear && $ticket->status != Tickets::STATUS_CLOSED
        ]);
    }

    /**
     * Create ticket message
     * @param int $id
     * @return array
     */
    public function actionCreateTicket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $customer = Customers::findOne(Yii::$app->user->identity->id);

        $model = new CreateTicketForm();
        $model->setCustomer($customer);

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
            'error' => 'Can not create ticket'
        ];
    }

    /**
     * Support page
     * @return string
     */
    public function actionSupport()
    {
        $this->view->title = Yii::t('app', 'pages.title.support');

        $model = new CreateTicketForm();
        $customer = Customers::findOne(Yii::$app->user->identity->id);
        $model->setCustomer($customer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/support');
        }

        $tickets = new TicketsSearch();
        $tickets->setParams([
            'customer_id' => $customer->id
        ]);

        return $this->render('support', [
            'model' => $model,
            'tickets' => $tickets->search(),
            'note' => Content::getContent('support'),
            'accesses' => [
                'canCreate' => Tickets::canCreate(Yii::$app->user->identity->id)
            ]
        ]);
    }

    /**
     * Invoice page
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionInvoice($id)
    {
        $invoice = Invoices::find()
            ->andWhere(['code' => $id])
            ->joinWith([
                'lastPayment'
            ])
            ->one();

        if (!$invoice) {
            return $this->redirect('/');
        }

        $this->view->title = Yii::t('app', 'pages.title.invoice', [
            'id' => $invoice->id
        ]);

        $paymentGateway = PaymentGateway::find()
            ->active()
            ->all();

        $payWait = Payments::findOne([
            'iid' => $invoice->id,
            'status' => Payments::STATUS_WAIT
        ]);

        $paymentsList = ArrayHelper::map($paymentGateway, 'pgid', 'name');

        return $this->render('invoice', [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'paymentsList' => $paymentsList,
            'payWait' => !!$payWait,
            'pgid' => $payWait ? $payWait->type : key($paymentsList)
        ]);
    }

    /**
     * View Invoices list
     * @return string
     */
    public function actionInvoices()
    {
        $this->view->title = Yii::t('app', 'pages.title.invoices');

        $invoices = new InvoicesSearch();
        $invoices->setParams([
            'customer_id' => Yii::$app->user->identity->id
        ]);

        return $this->render('invoices', [
            'invoices' => $invoices->search(),
        ]);
    }

    /**
     * Settings page
     * @return string|\yii\web\Response
     */
    public function actionSettings()
    {   
        $this->view->title = Yii::t('app', 'pages.title.settings');

        $customer = Customers::findOne(Yii::$app->user->identity->id);

        $model = new SettingsForm();
        $model->setCustomer($customer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/settings');
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
    }

    /**
     * Sign up action
     * @return string|\yii\web\Response
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->view->title = Yii::t('app', 'pages.title.create_account');

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Sign in action
     * @return string|\yii\web\Response
     */
    public function actionSignin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->view->title = Yii::t('app', 'pages.title.signin');

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if ($this->hasActiveInvoice()) {
                return $this->redirect('/invoices');
            } else {
                return $this->redirect('/panels');
            }
        }

        return $this->render('signin', [
            'model' => $model
        ]);
    }

    /**
     * Restore customer password
     * @return string|\yii\web\Response
     */
    public function actionRestore()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->view->title = Yii::t('app', 'pages.title.restore');

        $model =  new RestoreForm();

        if ($model->load(Yii::$app->request->post()) && $model->restore()) {
            Yii::$app->session->set('success', Content::getContent('forgot_email_sent'));
            return $this->refresh();
        }

        return $this->render('restore', [
            'model' => $model,
            'success' => Yii::$app->session->has('success'),
            'successMessage' => Yii::$app->session->get('success')
        ]);
    }

    /**
     * Reset customer password
     * @param string $token
     * @return \yii\web\Response
     */
    public function actionReset($token)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (!($customer = Customers::findOne(['token' => $token]))) {
            throw new NotFoundHttpException();
        }

        $this->view->title = Yii::t('app', 'pages.title.reset');

        $model = new ResetPasswordForm();
        $model->setCustomer($customer);

        if ($model->load(Yii::$app->request->post()) && $model->reset()) {
            if ($this->hasActiveInvoice()) {
                return $this->redirect('/invoices');
            } else {
                return $this->redirect('/panels');
            }
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }

    /**
     * Change email modal action
     * @return array
     */
    public function actionChangeemail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ChangeEmailForm();

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
            'error' => Yii::t('app', 'error.customer.can_not_change_email')
        ];
    }

    /**
     * Change password modal action
     * @return array
     */
    public function actionChangepassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ChangePasswordForm();

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
            'error' => Yii::t('app', 'error.customer.can_not_change_password')
        ];
    }

    /**
     * Logout page
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Redirect action
     * @param string $url
     * @return Response
     */
    public function actionRedirect($url)
    {
        $url = (FALSE !== strpos($url, 'http') ? $url : 'http://' . $url);
        return $this->redirect($url);
    }


    /**
     * Check if customer have active invoice
     * @return null|Invoices
     */
    private function hasActiveInvoice()
    {
        return Invoices::findOne([
            'cid' => Yii::$app->user->identity->id,
            'status' => 0
        ]);
    }

    /**
     * Find model by id and class name
     * @param int $id
     * @param string $class - class name
     * @return Response
     */
    private function findModel($id, $class)
    {
        if (!($model = ('\common\models\panels\\' . $class)::findOne([
            'cid' => Yii::$app->user->identity->id,
            'id' => $id
        ]))) {
            $this->redirect('/');
            return Yii::$app->end();
        }

        return $model;
    }

    /**
     * Checkout
     * @param string $id
     * @return string|Response|void
     */
    public function actionCheckout($id)
    {
        $this->view->title = Yii::t('app', 'pages.title.checkout');
        $invoice = Invoices::findOne(['code' => $id]);
        if ($invoice !== null) {

            if (!empty($_POST['pgid'])) {
                $paymentGateway = PaymentGateway::findOne(['pgid' => $_POST['pgid'], 'visibility' => 1, 'pid' => -1]);
                if ($paymentGateway !== null) {

                    $invoiceDetails = $invoice->invoiceDetails;
                    $paymentAmount = $invoice->getPaymentAmount();
                    $description = Yii::t('app', 'invoices.checkout.description', [
                        'invoice' => $invoice->id
                    ]);

                    if (empty($invoiceDetails) || !$paymentAmount) {
                        return $this->redirect('/');
                    }

                    $invoiceDetails = array_shift($invoiceDetails);

                    $panel = $invoiceDetails->panel;
                    $panelDomain = $invoiceDetails->domain;

                    if (!$panel) {
                        return $this->redirect('/');
                    }

                    $paymentsModel = new Payments();
                    $paymentsModel->load(array('Payments' => array(
                        'pid' => $panel->id,
                        'iid' => $invoice->id,
                        'date' => time(),
                        'type' => $_POST['pgid'],
                        'amount' => $paymentAmount,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                    )));
                    $paymentsModel->save();
                    switch ($_POST['pgid']) {
                        case "1":
                            $requestParams = array(
                                'RETURNURL' => 'https://'.$_SERVER['HTTP_HOST'].'/paypalexpress/'.$paymentsModel->id,
                                'CANCELURL' => 'https://'.$_SERVER['HTTP_HOST'].'/invoices'
                            );

                            $orderParams = array(
                                'PAYMENTREQUEST_0_AMT' => $paymentsModel->amount,
                                'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
                                'BRANDNAME' => 'Perfect Panel',
                                'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
                                'PAYMENTREQUEST_0_ITEMAMT' => $paymentsModel->amount,
                                'NOSHIPPING' => 1,
                                'PAYMENTREQUEST_0_DESC' => $description,
                                'RETURNFMFDETAILS' => 1,
                            );

                            $items = [];

                            foreach ($invoice->getCountedInvoiceDetails() as $key => $item) {
                                $items = array_merge($items, [
                                    'L_PAYMENTREQUEST_0_NAME' . $key => $item->getDescription(),
                                    'L_PAYMENTREQUEST_0_AMT' . $key => $item->amount,
                                    'L_PAYMENTREQUEST_0_QTY' . $key => '1',
                                ]);
                            }

                            $paypal = new Paypal;
                            $response = $paypal->request('SetExpressCheckout', $requestParams + $orderParams + $items);

                            if (is_array($response) && $response['ACK'] == 'Success') {
                               return $paypal->checkout($response['TOKEN']);
                            }

                            break;

                        case "2":
                            $account = '';

                            $paymentGateway = json_decode($paymentGateway->options);

                            if (!empty($paymentGateway->account)) {
                                $account = $paymentGateway->account;
                            }

                            return $this->render('checkout', [
                                'paymentType' => 2,
                                'account' => $account,
                                'paymentId' => $paymentsModel->id,
                                'paymentDescription' => $description,
                                'amount' => $paymentsModel->amount
                            ]);
                            break;

                        case "3":
                            $purse = '';

                            $paymentGateway = json_decode($paymentGateway->options);

                            if (!empty($paymentGateway->purse)) {
                                $purse = $paymentGateway->purse;
                            }

                            return $this->render('checkout', [
                                'paymentType' => 3,
                                'purse' => $purse,
                                'paymentId' => $paymentsModel->id,
                                'paymentDescription' => $description,
                                'amount' => $paymentsModel->amount
                            ]);
                            break;

                        case "4":
                            $paymentGateway = json_decode($paymentGateway->options);

                            $bitcoinId = ArrayHelper::getValue($paymentGateway, 'id');
                            $secret = ArrayHelper::getValue($paymentGateway, 'secret');

                            $params = [
                                'callback_data' => $paymentsModel->id,
                                'amount' => $paymentsModel->amount,
                            ];

                            $headers = Bitcoin::getHeaderOptions('/gateways/' . $bitcoinId . '/orders', $secret, $params);

                            $response = CurlHelper::request(
                                'https://gateway.gear.mycelium.com/gateways/' . $bitcoinId . '/orders?' . http_build_query($params),
                                '',
                                $headers
                            );
                            $response = json_decode($response);

                            $paymentId = ArrayHelper::getValue($response, 'payment_id');

                            if ($paymentId) {
                                $url = 'https://gateway.gear.mycelium.com/pay/' . $paymentId;

                                return $this->render('checkout', [
                                    'paymentType' => 4,
                                    'paymentDescription' => $description,
                                    'url' => $url
                                ]);
                            }

                            break;

                        case "5":
                            $account_number = '';

                            $paymentGateway = json_decode($paymentGateway->options);

                            if (!empty($paymentGateway->account_number)) {
                                $account_number = $paymentGateway->account_number;
                            }

                            return $this->render('checkout', [
                                'paymentType' => 5,
                                'account_number' => $account_number,
                                'paymentId' => $paymentsModel->id,
                                'paymentDescription' => $invoiceDetails->description,
                                'amount' => $paymentsModel->amount,
                                'items' => $invoice->getCountedInvoiceDetails()
                            ]);
                            break;

                        case "6":
                            $paymentGateway = json_decode($paymentGateway->options);

                            return $this->render('checkout', [
                                'paymentType' => 6,
                                'merchantId' => ArrayHelper::getValue($paymentGateway,'merchant_id', null),
                                'paymentId' => $paymentsModel->id,
                                'paymentDescription' => $description,
                                'amount' => $paymentsModel->amount,
                            ]);
                            break;
                    }
                } else {
                    return $this->redirect('/');
                }
            } else {
                return $this->redirect('/');
            }
        } else {
            return $this->redirect('/');
        }
    }
}