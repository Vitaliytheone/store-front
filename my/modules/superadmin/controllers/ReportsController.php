<?php

namespace my\modules\superadmin\controllers;

use my\modules\superadmin\models\search\ReportsPaymentsSearch;
use Yii;

/**
 * Class ReportsController
 * @package my\modules\superadmin\controllers
 */
class ReportsController extends CustomController
{
    public $activeTab = 'reports';

    public $layout = 'superadmin_v2.php';

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
