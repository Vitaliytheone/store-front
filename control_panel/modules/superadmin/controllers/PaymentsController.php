<?php

namespace superadmin\controllers;

use control_panel\helpers\Url;
use common\models\panels\Payments;
use common\models\panels\PaymentsLog;
use superadmin\models\search\PaymentsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PaymentsController for the `superadmin` module
 */
class PaymentsController extends CustomController
{
    public $activeTab = 'payments';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.payments');

        $paymentsSearch = new PaymentsSearch();
        $paymentsSearch->setParams(Yii::$app->request->get());

        $status = Yii::$app->request->get('status', null);

        return $this->render('index', [
            'payments' => $paymentsSearch->search(),
            'navs' => $paymentsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'modes' => $paymentsSearch->getAggregatedModes(),
            'methods' => $paymentsSearch->getAggregatedMethods(),
            'filters' => $paymentsSearch->getParams(),
            'searchType' => $paymentsSearch->getSearchTypes(),
        ]);
    }

    /**
     * Get payment details
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDetails($id)
    {
        $payment = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $logs = PaymentsLog::find()->andWhere([
            'pid' => $payment->id
        ])->orderBy([
            'id' => SORT_DESC
        ])->all();

        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/_payment_details', [
                'logs' => $logs,
            ])
        ];
    }

    /**
     * Make payment active
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionMakeActive($id)
    {
        $payment = $this->findModel($id);

        $payment->makeActive();

        return $this->redirect(Url::toRoute('/payments'));
    }

    /**
     * Accept verified payment
     * @param $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionMakeAccepted($id)
    {
        $payment = $this->findModel($id);

        if ($payment->can('makeAccepted')) {
            $payment->complete();
        }

        $this->redirect(Url::toRoute('/payments'));
    }

    /**
     * Refund payment to payer
     * @param $id
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionMakeRefunded($id)
    {
        $payment = $this->findModel($id);

        if ($payment->can('makeRefunded')) {
            $payment->refund();
        }

        $this->redirect(Url::toRoute('/payments'));
    }

    /**
     * Mark as complete
     * @param $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionComplete($id)
    {
        $payment = $this->findModel($id);

        if ($payment->can('makeCompleted')) {
            $payment->complete();
        }

        $this->redirect(Url::toRoute('/payments'));
    }

    /**
     * Find payments model
     * @param $id
     * @return null|Payments
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Payments::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
