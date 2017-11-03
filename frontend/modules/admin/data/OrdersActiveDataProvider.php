<?php

namespace frontend\modules\admin\data;

use yii;
use yii\helpers\ArrayHelper;
use frontend\modules\admin\models\search\OrdersSearch;


/**
 * Class OrdersActiveDataProvider
 * @package frontend\modules\admin\data
 */
class OrdersActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * Make and return Orders with Suborders array
     * @return array
     */
    public function getOrdersWithSuborders()
    {
        $ordersRows = $this->getModels();
        $orderIds = array_keys($ordersRows);

        $db = yii::$app->store->getInstance()->db_name;
        $suborders = (new \yii\db\Query())
            ->select([
                'so.id suborder_id', 'so.order_id', 'so.package_id', 'pk.product_id',
                'so.amount', 'so.link', 'so.quantity', 'so.status', 'so.mode',
                'pr.name product_name',
            ])
            ->from("$db.suborders so")
            ->leftJoin("$db.packages pk",'so.package_id = pk.id')
            ->leftJoin("$db.products pr",'pk.product_id = pr.id')
            ->where(['so.order_id' => $orderIds])
            ->indexBy('suborder_id')
            ->all();

        // Populate each suborder by additional data
        array_walk($suborders, function(&$suborder){
            $status = $suborder['status'];
            $mode = $suborder['mode'];
            $suborder['status_caption'] = ArrayHelper::getValue(OrdersSearch::$statusFilters, [$status, 'caption'], $status);
            $suborder['mode_caption'] = ArrayHelper::getValue(OrdersSearch::$modeFilters, [$mode, 'caption'], $mode);
        });

        // Populate each order by additional data
        array_walk($ordersRows, function(&$order, $orderId) use ($suborders){
            $order['suborders'] =  array_filter($suborders, function($suborder) use ($orderId){
                return $suborder['order_id'] == $orderId;
            },ARRAY_FILTER_USE_BOTH);
        });

        return $ordersRows;
    }
}