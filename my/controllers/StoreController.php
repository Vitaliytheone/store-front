<?php

namespace my\controllers;

use common\models\panels\Orders;
use my\models\search\StoresSearch;
use Yii;
use yii\filters\AccessControl;

/**
 * Class StoreController
 * @package my\controllers
 */
class StoreController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * View Panels list
     * @return string
     */
    public function actionStores()
    {
        $this->view->title = Yii::t('app', 'pages.title.stores');

        $storesSearch = new StoresSearch();
        $storesSearch->setParams([
            'customer_id' => 1//Yii::$app->user->identity->id
        ]);

        return $this->render('stores', [
            'stores' => $storesSearch->search(),
            'accesses' => [
                'canCreate' => Orders::can('create_store', [
                    'customerId' => Yii::$app->user->identity->id
                ])
            ]
        ]);
    }
}
