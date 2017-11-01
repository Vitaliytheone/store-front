<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use frontend\modules\admin\models\OrderSearch;

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
     * Render found & filtered order list
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $ordersDataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'ordersDataProvider' => $ordersDataProvider,
            'orderSearchModel' => $searchModel
        ]);
    }

}