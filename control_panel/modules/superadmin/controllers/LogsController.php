<?php

namespace superadmin\controllers;

use superadmin\models\search\ApiKeysLogsSearch;
use superadmin\models\search\CreditsLogsSearch;
use Yii;
use control_panel\helpers\Url;
use superadmin\models\search\StatusLogsSearch;
use superadmin\models\search\ProviderLogsSearch;

/**
 * Class LogsController
 * @package superadmin\controllers
 */
class LogsController extends CustomController
{
    public $activeTab = 'logs';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(Url::toRoute('logs/status'));
    }

    /**
     * Render Status logs
     * @return string
     */
    public function actionStatus()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.status_logs');

        $searchModel = new StatusLogsSearch();
        $dataProvider = $searchModel->search();

        return $this->render('status', [
            'logs' => $searchModel->getModelsForView(),
            'pagination' => $dataProvider->getPagination(),
        ]);
    }

    /**
     * Render Providers logs
     * @return string
     */
    public function actionProviders()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.providers_log');

        $logsSearch = new ProviderLogsSearch();
        $logsSearch->setParams(Yii::$app->request->get());

        return $this->render('providers', [
            'logs' => $logsSearch->search(),
            'filters' => $logsSearch->getParams()
        ]);
    }

    /**
     * Render Api Keys log
     * @return string
     */
    public function actionApiKeys()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.api_keys_logs');

        $searchModel = new ApiKeysLogsSearch();

        $searchModel->setParams(Yii::$app->request->get());

        $dataProvider = $searchModel->search();

        $navs = [
            '0' => Yii::t('app/superadmin', 'logs.api_keys.nav.all'),
            '1' => Yii::t('app/superadmin', 'logs.api_keys.nav.svoi'),
            '2' => Yii::t('app/superadmin', 'logs.api_keys.nav.ne_svoi'),
        ];

        $searchType = [
            '1' => Yii::t('app/superadmin', 'logs.api_keys.search.type_panel'),
            '2' => Yii::t('app/superadmin', 'logs.api_keys.search.type_provider'),
            '3' => Yii::t('app/superadmin', 'logs.api_keys.search.type_in_use'),
            '4' => Yii::t('app/superadmin', 'logs.api_keys.search.type_key'),
        ];

        return $this->render('api_keys', [
            'logs' => $searchModel->getModelsForView(),
            'navs' => $navs,
            'searchType' => $searchType,
            'filters' => $searchModel->getParams(),
            'pagination' => $dataProvider->getPagination(),
        ]);
    }

    /**
     * Render Credits log
     * @return string
     */
    public function actionCredits()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.credits_logs');

        $searchModel = new CreditsLogsSearch();
        $dataProvider = $searchModel->search();

        return $this->render('credits', [
            'logs' => $searchModel->getModelsForView(),
            'pagination' => $dataProvider->getPagination()
        ]);
    }

}
