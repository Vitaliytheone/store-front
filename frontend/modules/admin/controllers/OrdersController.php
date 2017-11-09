<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use common\models\store\Suborders;
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

        return parent::beforeAction($action);
    }

    /**
     * Render found & filtered orders list
     * @return string
     */
    public function actionIndex()
    {
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
        $response->format = \yii\web\Response::FORMAT_JSON;

        $suborderId = $request->get('suborder_id');
        if (!$request->isAjax || !$suborderId) {
            throw new BadRequestHttpException();
        }
        $suborderModel = Suborders::findOne($suborderId);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }
        $orderDetails = $suborderModel->getDetails(false);
        if (!$orderDetails) {
            throw new NotFoundHttpException();
        }
        return ['details' => $orderDetails];
    }

    /**
     * Suborder `change status`  action
     * Accepted only allowed new statuses.
     * @throws Yii\web\NotFoundHttpException
     */
    public function actionChangeStatus()
    {
        $suborderId = yii::$app->getRequest()->get('suborder_id');
        $orderStatus = yii::$app->getRequest()->get('status');
        $currentFilters = yii::$app->getRequest()->get('filters');
        if (!$suborderId || !$orderStatus) {
            throw new BadRequestHttpException();
        }

        $isStatusAllowed = in_array($orderStatus, OrdersSearch::$acceptedStatuses);
        if (!$isStatusAllowed || !$suborderId || !$orderStatus) {
            throw new NotAcceptableHttpException();
        }
        $suborderModel = Suborders::findOne($suborderId);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }
        $isChangeActionDisallowed = in_array($suborderModel->status, OrdersSearch::$disallowedChangeStatusStatuses);
        if ($isChangeActionDisallowed) {
            throw new NotAcceptableHttpException();
        }

        $suborderModel->setAttribute('status', $orderStatus);
        $suborderModel->save();

        $queryParams = is_array($currentFilters) ? http_build_query($currentFilters) : null;
        $this->redirect(Url::to(["/admin/orders?$queryParams"]));
    }

    /**
     * Suborder `cancel` action
     * @throws Yii\web\NotFoundHttpException
     */
    public function actionCancel()
    {
        $suborderId = yii::$app->getRequest()->get('suborder_id');
        $currentFilters = yii::$app->getRequest()->get('filters');

        if (!$suborderId) {
            throw new NotAcceptableHttpException();
        }

        $suborderModel = Suborders::findOne($suborderId);
        if (!$suborderModel) {
            throw new NotFoundHttpException();
        }
        $isCancelDisallowed = in_array($suborderModel->status, OrdersSearch::$disallowedCancelStatuses);
        if ($isCancelDisallowed) {
            throw new NotAcceptableHttpException();
        }
        $suborderModel->setAttribute('status', Suborders::STATUS_CANCELED);
        $suborderModel->save();

        $queryParams = is_array($currentFilters) ? http_build_query($currentFilters) : null;
        $this->redirect(Url::to(["/admin/orders?$queryParams"]));
    }
}