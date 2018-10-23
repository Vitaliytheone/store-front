<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\Invoices;
use my\helpers\Url;
use common\models\panels\Orders;
use common\models\panels\ThirdPartyLog;
use my\modules\superadmin\models\search\OrdersSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use my\components\SuperAccessControl;
use yii\filters\VerbFilter;

/**
 * OrdersController for the `superadmin` module
 */
class OrdersController extends CustomController
{
    public $activeTab = 'orders';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'change-status'=> ['POST'],
                    'details' => ['GET'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['details'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'orders.title');

        $ordersSearch = new OrdersSearch();
        $ordersSearch->setParams(Yii::$app->request->get());

        $status = Yii::$app->request->get('status', null);

        return $this->render('index', [
            'orders' => $ordersSearch->search(),
            'navs' => $ordersSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $ordersSearch->getParams(),
            'items' => $ordersSearch->getAggregatedItems(),
        ]);
    }

    /**
     * Change order status
     * @param int $id
     * @param int $status
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        $order = $this->findModel($id);

        $order->changeStatus($status);

        return $this->redirect(Url::toRoute('/orders'));
    }

    /**
     * Get order details
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDetails($id)
    {
        $order = $this->findModel($id);

        $logs = ThirdPartyLog::find()->orWhere([
            'item_id' => $order->id,
            'item' => ThirdPartyLog::ITEM_ORDER
        ]);

        switch ($order->item) {
            case Orders::ITEM_BUY_PANEL:
            case Orders::ITEM_BUY_CHILD_PANEL:
                $logs->orWhere([
                    'item_id' => $order->item_id,
                    'item' => [
                        ThirdPartyLog::ITEM_BUY_PANEL,
                        ThirdPartyLog::ITEM_PROLONGATION_PANEL
                    ]
                ]);
            break;

            case Orders::ITEM_BUY_SSL:
                $logs->orWhere([
                    'item_id' => $order->item_id,
                    'item' => [
                        ThirdPartyLog::ITEM_BUY_SSL,
                        ThirdPartyLog::ITEM_PROLONGATION_SSL
                    ]
                ]);
            break;

            case Orders::ITEM_BUY_DOMAIN:
                $logs->orWhere([
                    'item_id' => $order->item_id,
                    'item' => [
                        ThirdPartyLog::ITEM_BUY_DOMAIN,
                        ThirdPartyLog::ITEM_PROLONGATION_DOMAIN
                    ]
                ]);
            break;
        }

        $logs = $logs->all();
        
        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/_order_details', [
                'order' => $order,
                'logs' => $logs
            ])
        ];
    }

    /**
     * Find order model
     * @param $id
     * @return null|Orders
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Orders::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
