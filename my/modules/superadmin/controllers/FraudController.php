<?php

namespace superadmin\controllers;


use Yii;
use superadmin\models\search\FraudReportsSearch;
use common\models\panels\PaypalFraudReports;
use my\components\SuperAccessControl;
use yii\filters\VerbFilter;
use my\helpers\Url;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
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
                    'reports' => ['GET'],
                    'reports-change-status' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['report-details']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['report-details'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        return $this->redirect('fraud/reports');
    }

    /**
     * Render reports list
     * @return string
     */
    public function actionReports()
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
     * @param $id
     * @return array
     */
    public function actionReportDetails($id)
    {
        $report = PaypalFraudReports::findOne(['id' => $id]);

        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/reports/_report_details', [
                'details' => $report->transaction_details,
            ])
        ];
    }
}
