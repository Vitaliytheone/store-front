<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\Customers;
use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\Invoices;
use my\modules\superadmin\models\forms\AddInvoiceEarningsForm;
use my\modules\superadmin\models\forms\AddInvoicePaymentForm;
use my\modules\superadmin\models\forms\CreateInvoiceForm;
use my\modules\superadmin\models\forms\EditInvoiceCreditForm;
use my\modules\superadmin\models\forms\EditInvoiceForm;
use my\modules\superadmin\models\search\InvoicesSearch;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use my\components\SuperAccessControl;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;

/**
 * InvoicesController for the `superadmin` module
 */
class InvoicesController extends CustomController
{
    public $activeTab = 'invoices';

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'edit' => ['POST'],
                    'create' => ['POST'],
                    'add-payment' => ['POST'],
                    'edit-credit' => ['POST'],
                    'add-earnings' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'edit',
                    'create',
                    'add-payment',
                    'edit-credit',
                    'add-earnings',
                    'cancel',
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
        $this->view->title = Yii::t('app', 'pages.title.invoices');

        $invoicesSearch = new InvoicesSearch();
        $invoicesSearch->setParams(Yii::$app->request->get());

        $status = Yii::$app->request->get('status', null);

        return $this->render('index', [
            'invoices' => $invoicesSearch->search(),
            'navs' => $invoicesSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $invoicesSearch->getParams(),
            'searchType' => $invoicesSearch->getSearchTypes(),
        ]);
    }

    /**
     * Edit invoice.
     *
     * @access public
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $invoice = $this->findModel($id);

        if (!$invoice->can('editTotal')) {
            throw new ForbiddenHttpException();
        }

        $model = new EditInvoiceForm();
        $model->setInvoice($invoice);

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
     * Create invoice.
     *
     * @access public
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CreateInvoiceForm();

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
     * Add payment.
     *
     * @access public
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAddPayment($id)
    {
        $invoice = $this->findModel($id);

        $model = new AddInvoicePaymentForm();
        $model->setInvoice($invoice);

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
     * Add payment.
     *
     * @access public
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionEditCredit($id)
    {
        $invoice = $this->findModel($id);

        $model = new EditInvoiceCreditForm();
        $model->setInvoice($invoice);

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
     * @param $invoice_id
     * @param $customer_id
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionAddEarnings($invoice_id, $customer_id)
    {
        $invoice = $this->findModel($invoice_id);
        $customer = $this->findCustomer($customer_id);

        $model = new AddInvoiceEarningsForm();
        $model->setInvoice($invoice);
        $model->setCustomer($customer);

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
     * Cancel invoice
     *
     * @access public
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCancel($id)
    {
        $invoice = $this->findModel($id);

        if (Invoices::STATUS_UNPAID == $invoice->status) {
            $invoice->status = Invoices::STATUS_CANCELED;
            $invoice->save(false);
        }
        
        $this->redirect(Url::toRoute('/invoices'));
    }

    /**
     * Find invoice model
     * @param $id
     * @return null|Invoices
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Invoices::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * Find customers model
     * @param $id
     * @return Customers|null
     * @throws NotFoundHttpException
     */
    protected function findCustomer($id)
    {
        $model = Customers::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
