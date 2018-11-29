<?php

namespace superadmin\controllers;

use common\models\panels\PaypalPayments;
use superadmin\models\search\FraudIncidentsSerach;
use superadmin\models\search\FraudPaymentsSearch;
use superadmin\models\search\FraudAccountsSearch;
use Yii;
use superadmin\models\search\FraudReportsSearch;
use common\models\panels\PaypalFraudReports;
use my\components\SuperAccessControl;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use my\helpers\Url;
use yii\web\Response;

/**
 * Class FraudController
 * @package superadmin\controllers
 */
class FraudController extends CustomController
{
    /** @var string Active navigation tab */
    public $activeTab = 'fraud';

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
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'reports-change-status' => ['POST'],
                    'incidents' => ['GET'],
                    'payments' => ['GET'],
                    'accounts' => ['GET'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'payment-details',
                    'report-details',
                ]
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'payment-details',
                    'report-details',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Render reports list
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.fraud_reports');

        $reports = new FraudReportsSearch();
        $reports->setParams(Yii::$app->request->get());

        return $this->render('reports', [
            'reports' => $reports->search(),
            'navs' => $reports->navs(),
            'filters' => $reports->getParams(),
        ]);
    }

    /**
     * Change status of report
     * @throws \yii\db\Exception
     */
    public function actionReportsChangeStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');

        $report = PaypalFraudReports::findOne($id);
        $report->changeStatus($status);

        $this->redirect(Url::toRoute(['/fraud/reports']));
    }

    /**
     * @param $id
     * @return array
     */
    public function actionReportDetails($id)
    {
        $report = PaypalFraudReports::findOne(['id' => $id]);

        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/reports/_report_details', [
                'details' => $report,
            ])
        ];
    }

    /**
     * Render incidents list
     * @return string
     */
    public function actionIncidents()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.fraud_incidents');

        $incidents = new FraudIncidentsSerach();
        $incidents->setParams(Yii::$app->request->get());

        return $this->render('incidents', [
            'incidents' => $incidents->search(),
            'filters' => $incidents->getParams(),
        ]);
    }

    /**
     * Render payments list
     * $return string
     */
    public function actionPayments()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.fraud_payments');

        $payments = new FraudPaymentsSearch();
        $payments->setParams(Yii::$app->request->get());

        return $this->render('payments', [
            'payments' => $payments->search(),
            'filters' => $payments->getFilters(),
            'searchTypes' => FraudPaymentsSearch::getSearchTypes(),
         ]);
    }

    /**
     * Render accounts list
     * @return string
     */
    public function actionAccounts()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.fraud_accounts');

        $accounts = new FraudAccountsSearch();
        $accounts->setParams(Yii::$app->request->get());

        return $this->render('accounts', [
            'accounts' => $accounts->search(),
            'filters' => $accounts->getParams(),
        ]);
    }

    /**
     * @param $id
     * @return array
     */
    public function actionPaymentDetails($id)
    {
        $payment = PaypalPayments::findOne($id);

        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/payments/_payment_details', [
                'details' => $payment->response,
            ])
        ];
    }
}
