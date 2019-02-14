<?php

namespace my\controllers;

use common\components\cdn\BaseCdn;
use common\components\cdn\Cdn;
use common\helpers\PaymentHelper;
use common\models\panels\Params;
use common\models\panels\services\GetGeneralPaymentMethodsService;
use my\components\ActiveForm;
use my\components\bitcoin\Bitcoin;
use common\components\filters\DisableCsrfToken;
use my\components\payments\Paypal;
use common\helpers\CurlHelper;
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
use common\models\panels\Payments;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;

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
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['signin', 'signup', 'restore', 'reset', 'checkout', 'invoice', 'payer-verify', 'error', 'redirect', 'paypal-verify'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'message' => ['POST'],
                    'create-ticket' => ['POST'],
                    'changeemail' => ['POST'],
                    'changepassword' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['message', 'create-ticket', 'changeemail', 'changepassword']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['message', 'create-ticket', 'changeemail', 'changepassword'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'token' => [
                'class' => DisableCsrfToken::class,
                'only' => ['checkout', 'invoices', 'invoice'],
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
        // Disable csrf-validation for logged-in user on SignIn form
        // Uses for prevent "Bad Request (#400): Unable to verify your data submission"
        // on form double submit
        if (!Yii::$app->user->isGuest && $this->action->id === 'signin') {
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
     * @throws \yii\base\Exception
     * @throws \yii\base\ExitException
     * @throws \yii\base\UnknownClassException
     */
    public function actionMessage($id)
    {
        $ticket = $this->findModel($id, 'Tickets');
        $customer = Customers::findOne(Yii::$app->user->identity->id);
        $cdn = Cdn::getCdn();

        $model = new CreateMessageForm();
        $model->setCustomer($customer);
        $model->setTicket($ticket);
        $model->setCdn($cdn);
        $model->post = Yii::$app->request->post('qs-file');


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
     * @throws \yii\base\ExitException
     */
    public function actionTicket($id, $clear = false)
    {
        /**
         * @var Tickets $ticket
         */
        $ticket = $this->findModel($id, 'Tickets');
        $ticket->makeReaded();

        $ticketMessages = TicketMessages::find()
            ->ticketView($ticket->id)
            ->all();

        return $this->renderPartial('ticket', [
            'ticketMessages' => $ticketMessages,
            'ticket' => $ticket,
            'showForm' => !$clear && $ticket->status != Tickets::STATUS_CLOSED,
        ]);
    }

    /**
     * Create ticket message
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\UnknownClassException
     */
    public function actionCreateTicket()
    {
        $customer = Customers::findOne(Yii::$app->user->identity->id);
        $cdn = Cdn::getCdn();

        $model = new CreateTicketForm();
        $model->setCustomer($customer);
        $model->setCdn($cdn);
        $model->post = Yii::$app->request->post('qs-file');

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
     * @var BaseCdn $cdn
     * @return string
     * @throws \yii\base\Exception
     * @throws \yii\base\UnknownClassException
     */
    public function actionSupport()
    {
        $this->activeTab = 'support';
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
            ],
        ]);
    }

    /**
     * Invoice page
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionInvoice($id)
    {
        /** @var $invoice Invoices */
        $invoice = Invoices::find()
            ->andWhere(['code' => $id])
            ->joinWith([
                'lastPayment',
                'invoiceDetails'
            ])
            ->one();

        if (!$invoice) {
            return $this->redirect('/');
        }

        $this->view->title = Yii::t('app', 'pages.title.invoice', [
            'id' => $invoice->id
        ]);

        $payWait = Payments::findOne([
            'iid' => $invoice->id,
            'status' => Payments::STATUS_WAIT
        ]);

        $paymentsList = ArrayHelper::map(Yii::$container->get(GetGeneralPaymentMethodsService::class, [1])->get(), 'code', 'name');

        return $this->render('invoice', [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'paymentsList' => $paymentsList,
            'payWait' => !!$payWait,
            'code' => $payWait ? $payWait->payment_method : key($paymentsList),
            'verificationWait' => $invoice->emailVerification() ? Content::getContent('paypal_verify_note', ['email' => $invoice->emailVerification()]) : null,
        ]);
    }

    /**
     * View Invoices list
     * @return string
     */
    public function actionInvoices()
    {
        $this->activeTab = 'invoices';

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
        $this->activeTab = 'settings';
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
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @return string
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
            return $this->goHome();
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
     * Checkout
     * @param string $id
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function actionCheckout($id)
    {
        $this->view->title = Yii::t('app', 'pages.title.checkout');
        $invoice = Invoices::findOne(['code' => $id]);
        $code = Yii::$app->request->post('code');

        if (!$code || !$invoice || !($paymentMethod = Params::get(Params::CATEGORY_PAYMENT, $code)) || !ArrayHelper::getValue($paymentMethod, 'visibility')) {
            return $this->redirect('/');
        }
        
        if (!$invoice->can('pay')) {
           throw new ForbiddenHttpException();
        }

        $type = PaymentHelper::getTypeByCode($code);

        $invoiceDetails = $invoice->invoiceDetails;
        $paymentAmount = $invoice->getPaymentAmount();
        $description = Yii::t('app', 'invoices.checkout.description', [
            'invoice' => $invoice->id
        ]);

        if (empty($invoiceDetails) || 0 > $paymentAmount) {
            return $this->redirect('/');
        }

        $invoiceDetails = array_shift($invoiceDetails);

        $panel = $invoiceDetails->panel;
        if (!$panel) {
            return $this->redirect('/');
        }

        if (0 == $paymentAmount) {
            $invoice->paid($code);
            return $this->redirect('/');
        }

        $paymentsModel = new Payments();
        $paymentsModel->load(array('Payments' => array(
            'pid' => $panel->id,
            'iid' => $invoice->id,
            'date' => time(),
            'type' => $type,
            'payment_method' => $code,
            'amount' => $paymentAmount,
            'ip' => $_SERVER['REMOTE_ADDR'],
        )));
        $paymentsModel->save();

        switch ($code) {
            case Params::CODE_PAYPAL:
                $requestParams = array(
                    'RETURNURL' => 'http://'.$_SERVER['HTTP_HOST'].'/paypalexpress/' . $paymentsModel->id,
                    'CANCELURL' => 'http://'.$_SERVER['HTTP_HOST'].'/invoices'
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

                $paypal = new Paypal();
                $response = $paypal->request('SetExpressCheckout', $requestParams + $orderParams + $items);

                if (is_array($response) && $response['ACK'] == 'Success') {
                    return $paypal->checkout($response['TOKEN']);
                }

                break;

            case Params::CODE_PERFECT_MONEY:
                $account = ArrayHelper::getValue($paymentMethod, ['credentials', 'account'], '');

                return $this->render('checkout', [
                    'paymentType' => 2,
                    'account' => $account,
                    'paymentId' => $paymentsModel->id,
                    'paymentDescription' => $description,
                    'amount' => $paymentsModel->amount
                ]);
                break;

            case Params::CODE_WEBMONEY:
                $purse = ArrayHelper::getValue($paymentMethod, ['credentials', 'purse'], '');

                return $this->render('checkout', [
                    'paymentType' => 3,
                    'purse' => $purse,
                    'paymentId' => $paymentsModel->id,
                    'paymentDescription' => $description,
                    'amount' => $paymentsModel->amount
                ]);
                break;

            case Params::CODE_BITCOIN:
                $bitcoinId = ArrayHelper::getValue($paymentMethod, ['credentials', 'id'], '');
                $secret = ArrayHelper::getValue($paymentMethod, ['credentials', 'secret'], '');

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

            case Params::CODE_TWO_CHECKOUT:
                $account_number = ArrayHelper::getValue($paymentMethod, ['credentials', 'account_number'], '');

                return $this->render('checkout', [
                    'paymentType' => 5,
                    'account_number' => $account_number,
                    'paymentId' => $paymentsModel->id,
                    'paymentDescription' => $invoiceDetails->description,
                    'amount' => $paymentsModel->amount,
                    'items' => $invoice->getCountedInvoiceDetails()
                ]);
                break;

            case Params::CODE_COINPAYMENTS:

                return $this->render('checkout', [
                    'paymentType' => 6,
                    'merchantId' => ArrayHelper::getValue($paymentMethod, ['credentials', 'merchant_id'], null),
                    'paymentId' => $paymentsModel->id,
                    'paymentDescription' => $description,
                    'amount' => $paymentsModel->amount,
                ]);
                break;
        }

        return $this->redirect('/');
    }

    /**
     * Paypal payer payment verification
     * @param $code
     * @return Response
     * @throws \yii\base\Exception
     */
    public function actionPaypalVerify($code)
    {
        $payment = Payments::findOne([
            'verification_code' => $code,
            'status' => Payments::STATUS_VERIFICATION,
        ]);

        if (!$payment || !$payment->invoice) {
            return $this->redirect('/invoices');
        }

        $payment->verified();

        return $this->redirect('/invoices/' . $payment->invoice->code);
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
     * @throws \yii\base\ExitException
     */
    private function findModel($id, $class)
    {
        if (!($model = ('\common\models\panels\\' . $class)::findOne([
            'customer_id' => Yii::$app->user->identity->id,
            'id' => $id
        ]))) {
            $this->redirect('/');
            return Yii::$app->end();
        }

        return $model;
    }
}
