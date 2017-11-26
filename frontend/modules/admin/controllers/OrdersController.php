<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;

use common\models\store\Suborders;
use frontend\modules\admin\models\forms\SubordersListForm;

use frontend\modules\admin\models\search\OrdersSearch;

/**
 * Class OrdersController
 * @package frontend\modules\admin\controllers
 */
class OrdersController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->addModule('ordersDetails');
        $this->addModule('ordersClipboard');

        return parent::beforeAction($action);
    }

    /**
     * Render found & filtered orders list
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('admin', 'orders.page_title');

        $searchModel = new OrdersSearch();
        $ordersDataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'ordersDataProvider' => $ordersDataProvider,
            'ordersSearchModel' => $searchModel
        ]);
    }

    /**
     * Return Order details data AJAX action
     * GET /admin/orders/get-order-details?suborder_id={{suborderId}}
     *
     * @throws Yii\web\BadRequestHttpException
     * @throws Yii\web\NotFoundHttpException
     * @return array
     */
    public function actionGetOrderDetails()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        $suborderId = $request->get('suborder_id');
        if (!$request->isAjax || !$suborderId) {
            throw new BadRequestHttpException();
        }
        
        $suborderModel = SubordersListForm::findOne($suborderId);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }
        
        $orderDetails = $suborderModel->getDetails();
        if (!$orderDetails) {
            throw new NotFoundHttpException();
        }
        
        return ['details' => $orderDetails];
    }

    /**
     * Suborder `change status`  action
     * Accepted only allowed new statuses.
     * @param $id
     * @param $status
     * @throws Yii\web\BadRequestHttpException
     * @throws Yii\web\NotFoundHttpException
     * @throws Yii\web\NotAcceptableHttpException
     */
    public function actionChangeStatus($id, $status)
    {
        $filters = yii::$app->getRequest()->get('filters');

        $suborderModel = SubordersListForm::findOne($id);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }

        $suborderModel->setScenario(SubordersListForm::SCENARIO_CHANGE_STATUS_ACTION);
        if (!$suborderModel->validate()) {
            throw new NotAcceptableHttpException();
        }

        $suborderModel->setScenario(SubordersListForm::SCENARIO_CHANGE_STATUS_ACTION_ATTR);
        $suborderModel->setAttributes([
            'status' => $status,
            'mode' => Suborders::MODE_MANUAL,
        ]);

        if (!$suborderModel->save()) {
            throw new NotAcceptableHttpException();
        }

        Yii::$app->session->addFlash('messages', [
            'success' => Yii::t('admin', 'orders.message_status_changed')
        ]);

        $queryParams = is_array($filters) ? http_build_query($filters) : null;

        $this->redirect(Url::to(["/admin/orders?$queryParams"]));
    }

    /**
     * Suborder `cancel` action
     * @param $id
     * @throws Yii\web\NotFoundHttpException
     * @throws Yii\web\NotAcceptableHttpException
     */
    public function actionCancel($id)
    {
        $filters = yii::$app->getRequest()->get('filters');

        $suborderModel = SubordersListForm::findOne($id);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }

        $suborderModel->setScenario(SubordersListForm::SCENARIO_CANCEL_ACTION);
        if (!$suborderModel->validate()) {
            throw new NotAcceptableHttpException();
        };

        $suborderModel->setAttribute('status', Suborders::STATUS_CANCELED);
        $suborderModel->save(false);

        Yii::$app->session->addFlash('messages', [
            'success' => Yii::t('admin', 'orders.message_canceled')
        ]);

        $queryParams = is_array($filters) ? http_build_query($filters) : null;

        $this->redirect(Url::to(["/admin/orders?$queryParams"]));
    }

    /**
     * Suborder `resend` action
     * @param $id
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionResend($id)
    {
        $filters = yii::$app->getRequest()->get('filters');

        $suborderModel = SubordersListForm::findOne($id);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }

        $suborderModel->setScenario(SubordersListForm::SCENARIO_RESEND_ACTION);
        if (!$suborderModel->validate()) {
            throw new NotAcceptableHttpException();
        }

        $suborderModel->setAttributes([
            'status' => Suborders::STATUS_AWAITING,
            'send' => Suborders::RESEND_NO,
        ]);

        $suborderModel->save(false);

        Yii::$app->session->addFlash('messages', [
            'success' => Yii::t('admin', 'orders.message_resend')
        ]);

        $queryParams = is_array($filters) ? http_build_query($filters) : null;

        $this->redirect(Url::to(["/admin/orders?$queryParams"]));
    }

}