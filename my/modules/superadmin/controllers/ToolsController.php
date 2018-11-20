<?php

namespace superadmin\controllers;

use common\models\panels\PaypalFraudReports;
use my\components\ActiveForm;
use common\models\panels\SuperToolsScanner;
use superadmin\models\forms\PanelsScannerAddDomainForm;
use superadmin\models\search\DbHelperSearch;
use superadmin\models\search\FraudReportsSearch;
use superadmin\models\search\PanelsScannerSearch;
use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use my\helpers\Url;

/**
 * Class ToolsController
 * @package superadmin\controllers
 */
class ToolsController extends CustomController
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'levopanel'=> ['GET'],
                    'rentalpanel'=> ['GET'],
                    'panelfire' => ['GET'],
                    'fraud-reports' => ['GET'],
                    'reports-change-status' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['add-domain']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['add-domain'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }

    /** @var string Active navigation tab */
    public $activeTab = 'tools';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
       return $this->redirect('tools/levopanel');
    }

    /**
     * Render Levopanel domains list
     * @param null $status
     * @return string
     */
    public function actionLevopanel($status = null)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.levopanel');
        $this->addModule('superadminToolsControllerLevopanelAction');

        $search = new PanelsScannerSearch();
        $search->setPanel(SuperToolsScanner::PANEL_LEVOPANEL);

        return $this->render('panels_scanner', [
            'panels' => $search->searchPanels($status),
            'statusButtons' => $search->getStatusButtons(),
            'status' => $status,
            'panelType' => $search->getPanel(),
        ]);
    }


    /**
     * Render Retalpanel domains list
     * @param null $status
     * @return string
     */
    public function actionRentalpanel($status = null)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.rentalpanel');
        $this->addModule('superadminToolsControllerLevopanelAction');

        $search = new PanelsScannerSearch();
        $search->setPanel(SuperToolsScanner::PANEL_RENTALPANEL);

        return $this->render('panels_scanner', [
            'panels' => $search->searchPanels($status),
            'statusButtons' => $search->getStatusButtons(),
            'status' => $status,
            'panelType' => $search->getPanel(),
        ]);
    }

    /**
     * Render Panelfire domains list
     * @param null $status
     * @return string
     */
    public function actionPanelfire($status = null)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.smmfire');
        $this->addModule('superadminToolsControllerLevopanelAction');

        $search = new PanelsScannerSearch();
        $search->setPanel(SuperToolsScanner::PANEL_PANELFIRE);

        return $this->render('panels_scanner', [
            'panels' => $search->searchPanels($status),
            'statusButtons' => $search->getStatusButtons(),
            'status' => $status,
            'panelType' => $search->getPanel(),
        ]);
    }

    /**
     * Render DB helper page
     * @return string
     */
    public function actionDbHelper()
    {
        $this->view->title = Yii::t('app/superadmin', 'db_helper.title');

        $search = new DbHelperSearch();
        $search->setParams(Yii::$app->request->post());

        return $this->render('db_helper', [
            'model' => $search->search(),
            'query' => $search->getQueryForInput(),
            'selectedOption' => Yii::$app->request->post('db_name'),
            'selectList' => $search->getSelectList(),
        ]);
    }

    /**
     * Render Fraud Reports page
     * @return string
     */
    public function actionFraudReports()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tools.fraud_reports');

        $reports = new FraudReportsSearch();
        $reports->setParams(Yii::$app->request->get());

        return $this->render('fraud_reports', [
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

        $this->redirect(Url::toRoute(['/tools/fraud-reports']));
    }

    /**
     * Add new panel domain Ajax action
     * @param $panel string Name of the current scanner
     * @return array
     */
    public function actionAddDomain($panel)
    {
        $form = new PanelsScannerAddDomainForm();
        $form->setPanelInfo($panel);

        if (!$form->addDomain($panel, Yii::$app->request->post())) {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($form)
            ];
        }

        return [
            'status' => 'success',
        ];
    }
}
