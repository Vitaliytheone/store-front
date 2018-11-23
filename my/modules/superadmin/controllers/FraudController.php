<?php

namespace superadmin\controllers;

use superadmin\models\search\FraudIncidentsSerach;
use superadmin\models\search\FraudAccountsSearch;
use Yii;
use superadmin\models\search\FraudReportsSearch;
use common\models\panels\PaypalFraudReports;
use my\components\SuperAccessControl;
use yii\filters\VerbFilter;
use my\helpers\Url;

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
                    'accounts' => ['GET'],
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
}
