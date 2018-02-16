<?php
namespace console\components\sender;

use common\models\store\Suborders;
use common\models\stores\Stores;
use yii\base\Component;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SenderComponent extends Component
{
    /**
     * One-time orders sample limit
     * @var
     */
    public $ordersLimit = 2;

    private $_orders = [];

    /**
     * Get stores orders
     * with additional data about store and provider for each item
     * @return array
     */
    public function getOrders()
    {
        $stores = (new Query())
            ->select(['id', 'db_name'])
            ->from('stores')
            ->andWhere(['status' => Stores::STATUS_ACTIVE])
            ->andWhere(['not', ['db_name' => null]])
            ->andWhere(['not', ['db_name' => '']])
            ->indexBy('id')
            ->all();

        $orders = [];

        foreach ($stores as $storeId => $store) {

            $dbName = $store['db_name'];

            $requestLimit = $this->ordersLimit - count($orders);

            $orders += (new Query())
                ->select([
                    '*',
                    'store_id' => "CONCAT('$storeId')",
                    'db_name' => "CONCAT('$dbName')"
                ])
                ->from("$dbName.suborders")
                ->andWhere([
                    'status' => Suborders::STATUS_AWAITING,
                    'send' => Suborders::SEND_STATUS_AWAITING,
                ])
                ->orderBy(['id' => SORT_ASC])
                ->limit($requestLimit)
                ->all();

            if (count($orders) >= $this->ordersLimit) {
                break;
            }
        }

        $providers = (new Query())
            ->select(['id', 'site', 'protocol'])
            ->from('providers')
            ->indexBy('id')
            ->all();

        foreach ($orders as &$order) {
            $order['provider_host'] = $providers[$order['provider_id']]['site'];
            $order['protocol'] = $providers[$order['provider_id']]['protocol'];
        }

        return $orders;
    }

    /**
     * Return orders grouped by stores
     * @return array
     */
    private function _getOrdersByStores()
    {
        $ordersByStores = [];

        foreach ($this->_orders as $order) {
            $ordersByStores[$order['store_id']][] = $order;
        }

        return $ordersByStores;
    }

    private function _beforeSend(&$orders)
    {
        // Перед отправкой заказа менять store_db.suborders.send = 2
        print_r($this->_orders);
        print_r($this->_getOrdersByStores());

        $ordersByStores = $this->_getOrdersByStores();

        // Обновляем статус send для всех заказов каждого магазина
        


    }

    private function _afterSend(&$orders)
    {
        // После отправки менять на send = 3
    }

    public function run()
    {
        $this->_orders = $this->getOrders();

        $this->sendOrders($this->_orders);

    }



    private function sendOrders(&$orders)
    {
        $this->_beforeSend($orders);
        $this->_afterSend($orders);

//        $mh = curl_multi_init();
//        $connectionArray = [];
//
//        // Make requests pull
//        foreach($orders as $orderId => $order) {
//            $requestParams = array(
//                'order' => $order['provider_orderid'],
//                'key' => $order['api_key'],
//                'action' => Provider::API_ACTION_STATUS,
//            );
//
//            $curlOptions = array(
//                CURLOPT_URL => $order['site'],
//                CURLOPT_VERBOSE => false,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_POST => true,
//                CURLOPT_POSTFIELDS => http_build_query($requestParams),
//            );
//
//            $ch = curl_init();
//            curl_setopt_array($ch, $curlOptions);
//            curl_multi_add_handle($mh, $ch);
//            $connectionArray[$orderId] = $ch;
//        }
//
//        // Do requests
//        $running = null;
//        do {
//            curl_multi_exec($mh, $running);
//        } while ($running > 0);



    }

}