<?php
namespace console\components\sender;

use common\helpers\DbHelper;
use Yii;
use common\models\store\Suborders;
use common\models\stores\Stores;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Connection;
use yii\db\Query;

class SenderComponent extends Component
{

    /**
     * Api key
     * @var string
     */
    public $apiKey = 'd7cef90695d23ccdaa78546569def436';

    /**
     * One-time orders sample limit
     * @var
     */
    public $ordersLimit = 2;

    /**
     * Current stores orders list limited by $ordersLimit
     * Can contain orders from different stories
     * @var array
     */
    private $_orders = [];

    /**
     * Return DB connection component by DB name
     * @param $dbName
     * @return Connection
     */
    public function getStoreDbConnection($dbName)
    {
        $storeDbConnection = Yii::$app->storeDb;

        $host = DbHelper::getDsnAttribute('host', $storeDbConnection);
        $port = DbHelper::getDsnAttribute('port', $storeDbConnection);

        $connection = new Connection([
            'dsn' => 'mysql:' . 'host=' . $host . ';' . ($port ? 'port=' . $port . ';' : '') . 'dbname=' . $dbName,
            'username' => $storeDbConnection->username,
            'password' => $storeDbConnection->password,
        ]);

        return $connection;
    }

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
            $providerId = $order['provider_id'];
            $order['provider_host'] = $providers[$providerId]['site'];
            $order['protocol'] = $providers[$providerId]['protocol'];
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
            $storeId = $order['db_name'];
            $ordersByStores[$storeId][] = $order;
        }

        return $ordersByStores;
    }

    /**
     * Updating all current orders `send` status
     * @param $sendStatus
     * @throws Exception
     */
    private function _updateOrdersSendStatus($sendStatus)
    {
        if (!in_array($sendStatus, [
            Suborders::SEND_STATUS_SENDING,
            Suborders::SEND_STATUS_SENT,
        ])) {
            throw new Exception('Unexpected send status!');
        }

        $ordersByStores = $this->_getOrdersByStores();

        foreach ($ordersByStores as $storeDb => $storeOrders) {

            $db = $this->getStoreDbConnection($storeDb);
            $ordersIds = implode(',', array_column($storeOrders, 'id'));

            $db->createCommand('UPDATE suborders SET send = :send, updated_at = :updated_at WHERE id IN ' . "($ordersIds)")
                ->bindValue(':send', $sendStatus)
                ->bindValue(':updated_at', time())
                ->execute();
        }
    }

    /**
     * Run Sender
     */
    public function run()
    {
        $this->_orders = $this->getOrders();

        $this->_updateOrdersSendStatus(Suborders::SEND_STATUS_SENDING);

        $this->sendOrders($this->_orders);

    }
    

    private function sendOrders(&$orders)
    {


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