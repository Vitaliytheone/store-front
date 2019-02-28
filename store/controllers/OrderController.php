<?php

namespace store\controllers;

use common\models\store\Orders;
use store\models\search\OrdersSearch;
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
     * @param $code
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($code)
    {
        $order = $this->_findOrder($code);

        $this->pageTitle = Yii::t('app', 'vieworder.title', [
            'id' => $order->id
        ]);

        $search = new OrdersSearch();
        $search->setStore($this->store);
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
