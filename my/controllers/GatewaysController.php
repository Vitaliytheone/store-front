<?php

namespace my\controllers;


use common\models\panels\Content;
use common\models\panels\Customers;
use common\models\panels\Orders;
use my\models\Auth;
use my\models\forms\OrderGatewayForm;
use my\models\search\GatewaysSearch;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class GatewaysController
 * @package my\controllers
 */
class GatewaysController extends CustomController
{
    public $activeTab = 'gateway';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                ],
            ],
        ]);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app', 'pages.title.gateways');

        /** @var Customers $customer */
        $customer = Yii::$app->user->identity;

        $gatewaysSearch = new GatewaysSearch();
        $gatewaysSearch->setCustomer($customer);

        return $this->render('gateways', [
            'gateways' => $gatewaysSearch->search(),
            'accesses' => [
                'canCreate' => Orders::can('create_gateway', [
                    'customerId' => $customer->id
                ])
            ]
        ]);
    }

    /**
     * Create order
     * @return string|\yii\web\Response
     * @throws \Throwable
     */
    public function actionOrder()
    {
        /**
         * @var Auth $user
         */
        $user = Yii::$app->user->getIdentity();
        if (!Orders::can('create_panel', [
            'customerId' => $user->id
        ])) {
            return $this->redirect('/panels');
        }

        $this->view->title = Yii::t('app', 'pages.title.order');

        $model = new OrderGatewayForm();
        $model->setUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/invoices/' . $model->code);
        }

        return $this->render('order', [
            'model' => $model,
            'note' => Content::getContent('gateways_nameservers'),
            'user' => $user,
        ]);
    }
}
