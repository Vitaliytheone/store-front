<?php

namespace my\controllers;

use common\models\panels\Auth;
use common\models\panels\Customers;
use common\models\panels\Orders;
use my\models\forms\OrderStoreForm;
use my\models\search\StoresSearch;
use my\helpers\CustomerHelper;
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
            'customer_id' => Yii::$app->user->identity->id
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

    /**
     * Create store order
     * @return string
     */
    public function actionOrder()
    {
        $this->view->title = Yii::t('app', 'pages.title.order');

        /** @var Auth $user */
        $user = Yii::$app->user->getIdentity();

        $model = new OrderStoreForm();
        $model->setUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->createOrder()) {
            error_log('Order has been created!');
        }

        return $this->render('order', [
            'model' => $model,
        ]);
    }
}
