<?php
namespace sommerce\controllers;

use common\models\store\Orders;
use sommerce\models\search\OrdersSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Order controller
 */
class OrderController extends CustomController
{
    /**
     * Displays view order page.
     * @param string $code
     * @return string
     */
    public function actionView($code)
    {
        $order = $this->_findOrder($code);
        $store = Yii::$app->store->getInstance();

        $this->pageTitle = Yii::t('app', 'vieworder.title', [
            'id' => $order->id
        ]);

        $search = new OrdersSearch();
        $search->setStore($store);
        $search->setOrder($order);

        return $this->render('vieworder.twig', [
           'vieworder' => [
               'id' => $order->id,
               'customer_email' => $order->customer,
               'orders' => (array)ArrayHelper::getValue($search->search(), 'models', [])
           ]
        ]);
    }

    /**
     * Find order
     * @param string $code
     * @return Orders
     * @throws NotFoundHttpException
     */
    protected function _findOrder($code)
    {
        $order = null;

        if (empty($code) || !($order = Orders::findOne([
                'code' => $code,
            ]))) {
            throw new NotFoundHttpException();
        }

        return $order;
    }
}
