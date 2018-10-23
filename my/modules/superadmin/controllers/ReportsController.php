<?php

namespace superadmin\controllers;

use superadmin\models\search\ReportsPaymentsSearch;
use Yii;

/**
 * Class ReportsController
 * @package superadmin\controllers
 */
class ReportsController extends CustomController
{
    public $activeTab = 'reports';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
       return $this->redirect('reports/payments');
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionPayments()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.reports');

        $searchModel = new ReportsPaymentsSearch();
        $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'reportData' => $searchModel->getYearReportTable(),
            'paymentGateways' => $searchModel->getPaymentGatewaysForView(),
            'filters' => $searchModel->getFilters(),
            'years' => $searchModel->getYearsForView(),
        ]);
    }
}
