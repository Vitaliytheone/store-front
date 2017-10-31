<?php

namespace frontend\modules\admin\data;

use yii\helpers\ArrayHelper;
use frontend\modules\admin\models\OrderSearch;


/**
 * Class OrdersActiveDataProvider
 * @package frontend\modules\admin\data
 */
class OrdersActiveDataProvider extends \yii\data\ActiveDataProvider
{
    public function getOrdersSuborders()
    {
        $ordersRows = $this->getModels();
        $ordersSuborders = [];
        $orderIds = array_unique(array_column($ordersRows, 'order_id'));

        foreach ($orderIds as $orderId) {
            $currentOrderRowsKey = array_search($orderId, array_column($ordersRows, 'order_id'));
            $firstOrderRaw = $ordersRows[$currentOrderRowsKey];
            $customer = $firstOrderRaw['customer'];
            $createdAt = $firstOrderRaw['created_at'];

            // Get all raw Suborders for current Order. Reset all first-level keys.
            $suborders = array_values(array_filter($ordersRows, function($orderRowItem) use ($orderId){
                return $orderId == ArrayHelper::getValue($orderRowItem, 'order_id', null);
            }));

            // Set additional suborders data
            array_walk($suborders, function(&$suborder, $key){
                $status = $suborder['status'];
                $mode = $suborder['mode'];
                $suborder['status_caption'] = ArrayHelper::getValue(OrderSearch::$statusFilters, [$status,'caption'], $status);
                $suborder['mode_caption'] = ArrayHelper::getValue(OrderSearch::$modeFilters, [$mode, 'caption'], $mode);
            });

            $ordersSuborders[$orderId] = [
                'id' => $orderId,
                'customer' => $customer,
                'created_at' => $createdAt,
                'suborders' => $suborders,
            ];
        }

        return $ordersSuborders;
    }
}