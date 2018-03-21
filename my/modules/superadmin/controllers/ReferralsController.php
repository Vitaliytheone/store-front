<?php

namespace my\modules\superadmin\controllers;

use my\helpers\Url;
use common\models\panels\Customers;
use common\models\panels\Orders;
use common\models\panels\ThirdPartyLog;
use my\modules\superadmin\models\search\OrdersSearch;
use my\modules\superadmin\models\search\ReferralEarningsSearch;
use my\modules\superadmin\models\search\ReferralsPaymentsSearch;
use my\modules\superadmin\models\search\ReferralsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ReferralsController for the `superadmin` module
 */
class ReferralsController extends CustomController
{
    public $activeTab = 'referrals';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.referrals');

        $search = new ReferralsSearch();
        $search->setParams($_GET);

        return $this->render('index', [
            'referrals' => $search->search(),
            'filters' => $search->getParams()
        ]);
    }

    /**
     * Render total visits
     * @param $id
     * @return string
     */
    public function actionTotalVisits($id)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.total_visits');

        $customer = $this->findModel($id);

        return $this->render('total_visits', [
            'customer' => $customer,
        ]);
    }

    /**
     * Render total visits
     * @param $id
     * @return string
     */
    public function actionTotalEarnings($id)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.total_earnings');

        $customer = $this->findModel($id);

        $referralEarnings = new ReferralEarningsSearch();
        $referralEarnings->setParams([
            'referral' => $customer->id
        ]);

        return $this->render('total_earnings', [
            'referralEarnings' => $referralEarnings->search(),
        ]);
    }

    /**
     * Render paid referrals
     * @param $id
     * @return string
     */
    public function actionPaidReferrals($id)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.paid_referrals');

        $customer = $this->findModel($id);

        $search = new ReferralsPaymentsSearch();
        $search->setCustomer($customer);
        $search->setParams([
            'paid' => 1
        ]);

        return $this->render('referrals', [
            'referrals' => $search->search(),
        ]);
    }

    /**
     * Render paid referrals
     * @param $id
     * @return string
     */
    public function actionUnpaidReferrals($id)
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.unpaid_referrals');

        $customer = $this->findModel($id);

        $search = new ReferralsPaymentsSearch();
        $search->setCustomer($customer);
        $search->setParams([
            'paid' => 0
        ]);

        return $this->render('referrals', [
            'referrals' => $search->search(),
        ]);
    }

    /**
     * Find customer model
     * @param $id
     * @return null|Customers
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Customers::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
