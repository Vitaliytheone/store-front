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
        $foundOrdersDataProvider = $searchModel->search(Yii::$app->request->get());

        error_log(print_r($foundOrdersDataProvider->getOrdersSuborders(),1),0);

        return $this->render('index', [
            'foundOrdersDataProvider' => $foundOrdersDataProvider,
        ]);
    }

}