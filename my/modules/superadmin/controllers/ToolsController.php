<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use common\models\panels\SuperToolsScanner;
use my\modules\superadmin\models\forms\PanelsScannerAddDomainForm;
use my\modules\superadmin\models\search\PanelsScannerSearch;
use Yii;
use yii\web\Response;

/**
 * Class ToolsController
 * @package my\modules\superadmin\controllers
 */
class ToolsController extends CustomController
{
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
    public function actionRetalpanel($status = null)
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
     * Add new panel domain Ajax action
     * @param $panel string Name of the current scanner
     * @return array
     */
    public function actionAddDomain($panel)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

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
