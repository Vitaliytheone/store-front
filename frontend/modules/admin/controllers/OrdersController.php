<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use frontend\modules\admin\models\search\OrdersSearch;
use frontend\modules\admin\models\Suborder;

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
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
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
            'orderSearchModel' => $searchModel
        ]);
    }

    /**
     * Return Order details data AJAX action
     * GET /admin/orders/get-order-details?suborder_id={{suborderId}}
     *
     * @throws Yii\web\BadRequestHttpException
     * @throws Yii\web\NotFoundHttpException
     * @return $this|array
     */
    public function actionGetOrderDetails()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_JSON;

        $suborderId = $request->get('suborder_id');
        if (!$request->isAjax || !$suborderId) {
            throw new yii\web\BadRequestHttpException();
        }
        $suborderModel = Suborder::findOne($suborderId);
        if (!$suborderModel) {
            throw new yii\web\NotFoundHttpException();
        }
        $orderDetails = $suborderModel->getDetails();
        if (!$orderDetails) {
            throw new yii\web\NotFoundHttpException();
        }
        return $response->data = ['details' => $orderDetails];
    }

}