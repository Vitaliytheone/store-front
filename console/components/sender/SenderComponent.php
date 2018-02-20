<?php
namespace console\components\sender;

use common\models\stores\Providers;
use Yii;
use common\models\store\Suborders;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SenderComponent extends Component
{
    /**
     * One-time orders sample limit
     * @var integer
     */
    public $ordersLimit;

    /**
     * Current Send Orders list limited by $ordersLimit
     * @var array
     */
    private $_sendOrders = [];

    /**
     * Current DB connection
     * @var Connection
     */
    private $_db;

    /**
     * Set current DB connection
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->_db = $connection;
    }

    /**
     * Run Sender
     * @return array
     */
    public function run()
    {
        $this->_sendOrders = $this->getSendOrders();

        $this->_updateOrdersSendStatus(Suborders::SEND_STATUS_SENDING);

        $result = $this->sendOrders();

        return $result;
    }

    /**
     * Get queue send orders data
     * @return array
     */
    public function getSendOrders()
    {
        $sendOrders = (new Query())
            ->select([
                'so.id', 'so.store_id', 'so.provider_id', 'so.suborder_id', 'so.store_db',
                'pr.site', 'pr.protocol',
                'sprv.apikey'
            ])
            ->from(['so' => 'stores_send_orders'])
            ->leftJoin(['pr' => 'providers'], 'pr.id = so.provider_id')
            ->leftJoin(['sprv' => 'store_providers'], 'sprv.provider_id = so.provider_id AND sprv.store_id = so.store_id')
            ->limit($this->ordersLimit)
            ->all();

        // Delete all fetched SendOrders
        $sendOrdersIds = implode(',', array_column($sendOrders, 'id'));

        if ($sendOrdersIds) {
            Yii::$app->db->createCommand('DELETE FROM stores_send_orders WHERE id IN ' . "($sendOrdersIds)")->execute();
        }

        return $sendOrders;
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

        $sendOrdersByStores = [];

        foreach ($this->_sendOrders as $order) {
            $storeDB = $order['store_db'];
            $sendOrdersByStores[$storeDB][] = $order;
        }

        foreach ($sendOrdersByStores as $storeDb => $storeOrders) {

            $ordersIds = implode(',', array_column($storeOrders, 'suborder_id'));

            $this->_db->createCommand("UPDATE $storeDb.suborders SET send = :send, updated_at = :updated_at WHERE id IN ($ordersIds)")
                ->bindValue(':send', $sendStatus)
                ->bindValue(':updated_at', time())
                ->execute();
        }
    }

    /**
     * Update suborder by values
     * @param $orderInfo
     * @param $values
     */
    private function _updateOrder($orderInfo, $values)
    {
        $orderId = $orderInfo['suborder_id'];
        $storeDb = $orderInfo['store_db'];

        $defaultValues = [
            ':status' => null,
            ':send' => null,
            ':provider_order_id' => null,
            ':provider_response' => null,
            ':provider_response_code' => 0,
            ':updated_at' => time(),
        ];

        $values = array_intersect_key(array_merge($defaultValues, $values), $defaultValues);

        $this->_db->createCommand("UPDATE $storeDb.suborders SET 
              `status` = :status,
              `send` = :send,
              `provider_order_id` = :provider_order_id,
              `provider_response` = :provider_response,
              `provider_response_code` = :provider_response_code,
              `updated_at` = :updated_at
              WHERE `id` = :id
            ")
            ->bindValues($values)
            ->bindValue(':id', $orderId)
            ->execute();
    }


    /**
     * Resend order errored by protocol
     * @param $orderInfo
     */
    private function _resendOrder($orderInfo)
    {
        Yii::$app->db
            ->createCommand('UPDATE `providers` SET `protocol` = :protocol WHERE `id` = :id')
            ->bindValues([
                ':protocol' => Providers::PROTOCOL_HTTPS,
                ':id' => $orderInfo['provider_id'],
            ])
            ->execute();

        Yii::$app->db
            ->createCommand('
                  INSERT INTO `stores_send_orders` 
                  (`store_id`, `provider_id`, `suborder_id`, `store_db`) 
                  VALUES (:store_id, :provider_id, :suborder_id, :store_db);
             ')
            ->bindValues([
                'store_id' => $orderInfo['store_id'],
                'provider_id' => $orderInfo['provider_id'],
                'suborder_id' => $orderInfo['suborder_id'],
                'store_db'=> $orderInfo['store_db'],
            ])
            ->execute();

        $this->_updateOrder($orderInfo, [
            'send' => Suborders::SEND_STATUS_AWAITING,
        ]);
    }

    /**
     * Orders Sender and result processor
     * @return array
     */
    private function sendOrders()
    {
        $sendResults = [
            'total' => count($this->_sendOrders),
            'success' => 0,
            'resend' => 0,

            'err_curl' => 0,
            'err_http' => 0,
            'err_json' => 0,
            'err_other' => 0
        ];

        $mh = curl_multi_init();
        $connectionHandlers = [];

        /**
         * Make request pull
         */
        foreach($this->_sendOrders as $sendOrderId => $sendOrder) {

            $storeDb = $sendOrder['store_db'];

            $order = $this->_db->createCommand("SELECT * FROM $storeDb.suborders WHERE id = :id")
                ->bindValue(':id', $sendOrder['suborder_id'])
                ->queryOne();

            if (empty($order)) {
                continue;
            }

            $apiUrl = ($sendOrder['protocol'] == Providers::PROTOCOL_HTTPS ? 'https://' : 'http://') . $sendOrder['site'] . '/api/v2';

            $requestParams = array(
                'key' => $sendOrder['apikey'],
                'action' => Providers::API_ACTION_ADD,
                'service' => $order['provider_service'],
                'link' => $order['link'],
                'quantity' => $order['quantity'],
            );

            $curlOptions = array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_VERBOSE => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($requestParams),

                CURLOPT_PRIVATE => json_encode([
                    'store_id' => $sendOrder['store_id'],
                    'suborder_id' => $sendOrder['suborder_id'],
                    'provider_id' => $sendOrder['provider_id'],
                    'store_db' => $sendOrder['store_db'],
                    'protocol' => $sendOrder['protocol']
                ]),
            );

            $ch = curl_init();
            curl_setopt_array($ch, $curlOptions);
            curl_multi_add_handle($mh, $ch);
            $connectionHandlers[$sendOrderId] = $ch;
        }

        // Do requests
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        /**
         * Process results
         */
        foreach ($connectionHandlers as $ch)
        {
            $orderInfo = json_decode(curl_getinfo($ch, CURLINFO_PRIVATE), true);
            $protocol = $orderInfo['protocol'];

            // System Errors
            if (curl_errno($ch)) {
                $error = json_encode(curl_error($ch));
                $values = [
                    ':status' => Suborders::STATUS_ERROR,
                    ':send' => Suborders::SEND_STATUS_SENT,
                    ':provider_response' => $error,
                ];

                $this->_updateOrder($orderInfo, $values);

                $sendResults['err_curl']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            $requestInfo = curl_getinfo($ch);
            $responseRawResult = curl_multi_getcontent($ch);
            $responseResult = json_decode($responseRawResult, true);
            $responseCode = $requestInfo['http_code'];


            // Non HTTP 200 error
            if ($responseCode != 200) {
                $values = [
                    ':status' => Suborders::STATUS_ERROR,
                    ':send' => Suborders::SEND_STATUS_SENT,
                    ':provider_response' => $responseRawResult,
                    ':provider_response_code' => $responseCode,
                ];

                $this->_updateOrder($orderInfo, $values);

                $sendResults['err_http']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // Json decode errors
            if ((json_last_error() !== JSON_ERROR_NONE)) {

                $error = json_encode(json_last_error());
                $values = [
                    ':status' => Suborders::STATUS_ERROR,
                    ':send' => Suborders::SEND_STATUS_SENT,
                    ':provider_response' => $error,
                    ':provider_response_code' => $responseCode,
                ];

                $this->_updateOrder($orderInfo, $values);

                $sendResults['err_json']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // Provider service resend
            if (
                $protocol == Providers::PROTOCOL_HTTP &&
                ArrayHelper::getValue($responseResult, 'error') == 'Incorrect request'
            ) {
                $this->_resendOrder($orderInfo);

                $sendResults['resend']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // Success
            if (isset($responseResult['order'])) {
                $values = [
                    ':status' => Suborders::STATUS_COMPLETED,
                    ':send' => Suborders::SEND_STATUS_SENT,
                    ':provider_order_id' => $responseResult['order'],
                    ':provider_response' => $responseRawResult,
                    ':provider_response_code' => $responseCode,
                ];

                $this->_updateOrder($orderInfo, $values);

                $sendResults['success']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // All other situation
            $values = [
                ':status' => Suborders::STATUS_ERROR,
                ':send' => Suborders::SEND_STATUS_SENT,
                ':provider_response' => $responseRawResult,
                ':provider_response_code' => $responseCode,
            ];

            $this->_updateOrder($orderInfo, $values);

            $sendResults['err_other']++;

            curl_multi_remove_handle($mh, $ch);
        }

        curl_multi_close($mh);

        return $sendResults;
    }

}