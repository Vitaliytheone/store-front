<?php

namespace sommerce\components\sender;

use sommerce\events\Events;
use common\models\panels\AdditionalServices;
use common\models\panels\Getstatus;
use common\models\sommerce\Packages;
use common\models\sommerces\StoreProviders;
use common\models\sommerces\Stores;
use common\models\sommerces\StoresSendOrders;
use common\models\sommerce\Suborders;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SenderComponent
 * @package store\components\sender
 */
class SenderComponent extends Component
{
    const API_ACTION_PRIVATE = 'private';

    /**
     * One-time orders sample limit
     * @var integer
     */
    public $ordersLimit;

    /**
     * Sender API endpoint
     * @var
     */
    public $apiEndPoint;

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
     * Current store
     * @var Stores
     */
    private $_store;

    /**
     * Suborders table name
     * @var
     */
    private $_tableSuborders;

    /**
     * Packages table name
     * @var
     */
    private $_tablePackages;

    /**
     * StoresSendOrders table name
     * @var
     */
    private $_tableStoresSendOrders;


    /** @inheritdoc */
    public function init()
    {
        parent::init();

        $this->_tablePackages = Packages::tableName();
        $this->_tableSuborders = Suborders::tableName();
        $this->_tableStoresSendOrders = StoresSendOrders::tableName();
    }

    /**
     * Set current store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

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
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function run()
    {
        $this->_sendOrders = $this->getSendOrders();

        $this->_updateOrdersSendStatus(Suborders::SEND_STATUS_SENDING);

        return $this->sendOrders();
    }

    /**
     * Get queue send orders data
     * @return array
     * @throws \yii\db\Exception
     */
    public function getSendOrders()
    {
        $sendOrders = (new Query())
            ->select([
                'so.id', 'so.store_id', 'so.provider_id', 'so.suborder_id', 'so.store_db',
                'pr.name as site',
                'sprv.apikey'
            ])
            ->from(['so' => StoresSendOrders::tableName()])
            ->leftJoin(['pr' => AdditionalServices::tableName()], 'pr.provider_id = so.provider_id')
            ->leftJoin(['sprv' => StoreProviders::tableName()], 'sprv.provider_id = so.provider_id AND sprv.store_id = so.store_id')
            ->limit($this->ordersLimit)
            ->all();

        // Delete all fetched SendOrders
        $sendOrdersIds = implode(',', array_column($sendOrders, 'id'));

        /** TODO::: UNCOMM */

        if ($sendOrdersIds) {
            Yii::$app->db->createCommand("DELETE FROM $this->_tableStoresSendOrders WHERE id IN ($sendOrdersIds)")->execute();
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

            $this->_db->createCommand("
                    UPDATE $storeDb.$this->_tableSuborders 
                    SET 
                    send = :send,
                    updated_at = :updated_at
                    WHERE id IN ($ordersIds)
                ")
                ->bindValue(':send', $sendStatus)
                ->bindValue(':updated_at', time())
                ->execute();
        }
    }

    /**
     * Update suborder by values
     * @param $orderInfo
     * @param $values
     * @throws \yii\db\Exception
     */
    private function _updateOrder($orderInfo, $values)
    {
        $orderId = $orderInfo['suborder_id'];
        $storeDb = $orderInfo['store_db'];
        $newStatus = ArrayHelper::getValue($values, ':status');

        $defaultValues = [
            ':status' => null,
            ':send' => null,
            ':provider_order_id' => null,
            ':provider_response' => null,
            ':provider_response_code' => 0,
            ':provider_id' => null,
            ':provider_service' => null,
            ':updated_at' => time(),
        ];

        $values = array_intersect_key(array_merge($defaultValues, $values), $defaultValues);
        static::addGetstatus($orderInfo, $values);

        $this->_db->createCommand("
              UPDATE $storeDb.$this->_tableSuborders 
              SET 
              `status` = :status,
              `send` = :send,
              `provider_order_id` = :provider_order_id,
              `provider_response` = :provider_response,
              `provider_response_code` = :provider_response_code,
              `provider_id` = COALESCE(:provider_id, `provider_id`),
              `provider_service` = COALESCE(:provider_service, `provider_service`),
              `updated_at` = :updated_at
              WHERE `id` = :id
            ")
            ->bindValues($values)
            ->bindValue(':id', $orderId)
            ->execute();

        if (Suborders::STATUS_FAILED == $newStatus) {
            Events::add(Events::EVENT_STORE_ORDER_CHANGED_STATUS, [
                'suborderId' => $orderInfo['suborder_id'],
                'storeId' => $orderInfo['store_id'],
                'status' => $newStatus
            ]);
        }
    }

    /**
     * @param $orderInfo
     * @param $values
     */
    public static function addGetstatus($orderInfo, $values)
    {
        $orderId = $orderInfo['suborder_id'];
        $storeDb = $orderInfo['store_db'];

        if (!empty($values[':provider_order_id'])) {

            $storeId = (new Query())->select('id')
                ->from(Stores::tableName())
                ->where(['db_name' => $storeDb])
                ->one()['id'];

            $suborder = (new Query())->select(['apikey', 'link', 'overflow_quantity'])
                ->from($storeDb . ".suborders as s")
                ->leftJoin(['sp' => StoreProviders::tableName()], 'sp.provider_id = s.provider_id 
                    and sp.store_id = :store_id', [
                    ':store_id' => $storeId
                ])
                ->where(['s.id' => $orderId])
                ->one();

            $getstatus = new Getstatus();
            $getstatus->pid = $storeId;
            $getstatus->oid = $orderId;
            $getstatus->roid = $values[':provider_id'];
            $getstatus->login = $suborder['apikey'];
            $getstatus->apikey = '';
            $getstatus->store = 1;
            $getstatus->passwd = '';
            $getstatus->res = $values[':provider_order_id'];
            $getstatus->reid = $values[':provider_service'];
            $getstatus->page_id = $suborder['link'];
            $getstatus->count = $suborder['overflow_quantity'];
            $getstatus->start_count = 0;
            $getstatus->status = $values[':status'];
            $getstatus->type = Getstatus::TYPE_STORES_INTERNAL;
            $getstatus->save(false);
        }
    }

    /**
     * Orders Sender and result processor
     * @return array
     * @throws \yii\db\Exception
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

            $order = $this->_db->createCommand("
                SELECT `id`, `order_id`, `package_id`, `link`, `quantity`, `overflow_quantity`
                FROM $storeDb.$this->_tableSuborders
                WHERE id = :id
            ")
                ->bindValue(':id', $sendOrder['suborder_id'])
                ->queryOne();

            $orderPackage = $this->_db->createCommand("
                SELECT `provider_service`, `provider_id`
                FROM $storeDb.$this->_tablePackages
                WHERE id = :id
            ")
                ->bindValue(':id', $order['package_id'])
                ->queryOne();

            $provider = (new Query())
                ->select([
                    'id' => 'pr.id',
                    'api_key' => 'sprv.apiKey',
                    'site' => 'pr.name',
                    'type' => 'pr.type',
                ])
                ->from(['sprv' => StoreProviders::tableName()])
                ->leftJoin(['pr' => AdditionalServices::tableName()], 'pr.provider_id = sprv.provider_id')
                ->where([
                    'sprv.provider_id' => $orderPackage['provider_id'],
                    'sprv.store_id' => $sendOrder['store_id']
                ])
                ->one();

            if (empty($order) || empty($orderPackage) || empty($provider)) {
                continue;
            }

//          В соотв. с #ID-551
//          Убираем проверку http/https, все отпарвляем на локальный домен $this->apiEndPoint
//          $apiUrl = ($provider['protocol'] == Providers::PROTOCOL_HTTPS ? 'https://' : 'http://') . $provider['site'] . '/api/v2';
//          Добавляем параметр `domain`, в котором передаем домен провайдера

            $requestParams = array(
                'key' => $provider['api_key'],
                'action' => self::API_ACTION_PRIVATE,
                'service' => $orderPackage['provider_service'],
                'link' => $order['link'],
                'quantity' => $order['overflow_quantity'],
                'domain' => $provider['site'],
            );

            $curlOptions = array(
                CURLOPT_URL => $this->apiEndPoint,
                CURLOPT_VERBOSE => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($requestParams),

                CURLOPT_PRIVATE => json_encode([
                    'store_id' => $sendOrder['store_id'],
                    'suborder_id' => $sendOrder['suborder_id'],
                    'store_db' => $sendOrder['store_db'],
                    'provider_id' => $orderPackage['provider_id'],
                    'provider_service' => $orderPackage['provider_service'],
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
            // System Errors
            if (curl_errno($ch)) {
                $error = json_encode(curl_error($ch));
                $values = [
                    ':status' => Suborders::STATUS_FAILED,
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
                    ':status' => Suborders::STATUS_FAILED,
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
                    ':status' => Suborders::STATUS_FAILED,
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
            // * Resend orders routine removed according #ID-551

            // Success
            if (isset($responseResult['order'])) {
                $values = [
                    ':status' => Suborders::STATUS_PENDING,
                    ':send' => Suborders::SEND_STATUS_SENT,
                    ':provider_order_id' => $responseResult['order'],
                    ':provider_response' => $responseRawResult,
                    ':provider_response_code' => $responseCode,
                    ':provider_id' => $orderInfo['provider_id'],
                    ':provider_service' => $orderInfo['provider_service'],
                ];

                $this->_updateOrder($orderInfo, $values);

                $sendResults['success']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // All other situation
            $values = [
                ':status' => Suborders::STATUS_FAILED,
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
