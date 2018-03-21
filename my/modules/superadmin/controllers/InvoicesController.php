<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\Invoices;
use my\modules\superadmin\models\forms\AddInvoicePaymentForm;
use my\modules\superadmin\models\forms\CreateInvoiceForm;
use my\modules\superadmin\models\forms\EditInvoiceCreditForm;
use my\modules\superadmin\models\forms\EditInvoiceForm;
use my\modules\superadmin\models\search\InvoicesSearch;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * InvoicesController for the `superadmin` module
 */
class InvoicesController extends CustomController
{
    public $activeTab = 'invoices';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app', 'pages.title.invoices');

        $sslSearch = new InvoicesSearch();
        $sslSearch->setParams(Yii::$app->request->get());

        $status = Yii::$app->request->get('status', null);

        return $this->render('index', [
            'invoices' => $sslSearch->search(),
            'navs' => $sslSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $sslSearch->getParams()
        ]);
    }

    /**
     * Edit invoice.
     *
     * @access public
     * @param integer $id
     * @return mixed
     */
    public function actionEdit($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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
        Yii::$app->response->format = Response::FORMAT_JSON;

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
     */
    public function actionAddPayment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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
     */
    public function actionEditCredit($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * Cancel invoice
     *
     * @access public
     * @param integer $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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
}
