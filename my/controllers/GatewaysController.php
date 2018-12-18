<?php

namespace my\controllers;


use common\models\panels\Customers;
use common\models\panels\Orders;
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
}
