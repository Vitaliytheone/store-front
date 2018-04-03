<?php

namespace my\controllers;

use common\models\panels\Auth;
use common\models\panels\Customers;
use common\models\panels\Orders;
use my\models\forms\OrderStoreForm;
use my\models\search\StoresSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;

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
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            /**
                             * @var $customer Customers
                             */
                            $customer = Yii::$app->user->getIdentity();

                            if (!$customer || !$customer->can('stores')) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
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

        $request = Yii::$app->request;

        $model = new OrderStoreForm();
        $model->setUser($user);
        $model->setIp($request->getUserIP());
        $model->setTrial(!$user->hasStores());

        if (!$model->load($request->post()) || !$model->orderStore()) {
            return $this->render('order', [
                'model' => $model,
            ]);
        }

        if ($model->getTrial()) {
            return $this->redirect(Url::toRoute('/stores'));
        }

        return $this->redirect('/invoices/' . $model->getInvoiceCode());
    }
}
