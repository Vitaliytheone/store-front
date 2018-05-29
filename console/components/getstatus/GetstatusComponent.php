<?php
namespace console\components\getstatus;

use common\events\Events;
use Yii;
use common\models\store\Suborders;
use common\models\stores\Providers;
use common\models\stores\StoreProviders;
use common\models\stores\Stores;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class GetstatusComponent
 * @package console\components\getstatus
 */
class GetstatusComponent extends Component
{
    /**
     * One-time orders sample limit
     * @var integer
     */
    public $ordersLimit;

    /**
     * Get status current orders set
     * @var
     */
    private $_orders = [];

    /**
     * Current DB connection
     * @var Connection
     */
    private $_db;

    /**
     * Stores table name
     * @var
     */
    private $_tableStores;

    /**
     * Suborders table name
     * @var
     */
    private $_tableSuborders;

    /**
     * Providers table name
     * @var
     */
    private $_tableProviders;

    /**
     * Store providers table name
     * @var
     */
    private $_tableStoreProviders;

    /** @inheritdoc */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->_tableStores = Stores::tableName();
        $this->_tableSuborders = Suborders::tableName();
        $this->_tableProviders = Providers::tableName();
        $this->_tableStoreProviders = StoreProviders::tableName();
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
     * Run Getstatus
     * @return array
     */
    public function run()
    {
        $this->_orders = $this->_getOrders();

        return $this->_getStatus();
    }

    /**
     * Return processing orders from all stores.
     * limited by $ordersLimit and created time
     * @return array
     */
    private function _getOrders()
    {
        $stores = (new Query())
            ->select(['id', 'db_name'])
            ->from($this->_tableStores)
            ->andWhere(['status' => Stores::STATUS_ACTIVE])
            ->andWhere(['not', ['db_name' => null]])
            ->andWhere(['not', ['db_name' => '']])
            ->indexBy('id')
            ->all();

        $fromDate = time() - 30 * 24 * 60 * 60; // 30 Days ago
        $orders = [];

        // Get orders from all shops.
        //Total orders count limited by $ordersLimit
        foreach ($stores as $storeId => $store) {

            $requestLimit = $this->ordersLimit - count($orders);

            $storeProviders = (new Query())
                ->select([
                    'id' => 'pr.id',
                    'site' => 'pr.site',
                    'protocol' => 'pr.protocol',
                    'type' => 'pr.type',
                    'apikey' => 'sp.apikey',
                ])
                ->from(['sp' => $this->_tableStoreProviders])
                ->leftJoin(['pr' => $this->_tableProviders], 'pr.id = sp.provider_id')
                ->andWhere(['store_id' => $storeId])
                ->indexBy('id')
                ->all();

            $db = $store['db_name'];

            $newOrders = (new Query())
                ->select(['*'])
                ->from("$db.$this->_tableSuborders")
                ->andWhere([
                    'mode' => Suborders::MODE_AUTO
                ])
                ->andWhere([
                    'status' => [
                        Suborders::STATUS_PENDING,
                        Suborders::STATUS_IN_PROGRESS,
                        Suborders::STATUS_ERROR,
                    ]])
                ->andWhere(['>', 'updated_at', $fromDate])
                ->orderBy(['updated_at' => SORT_ASC])
                ->limit($requestLimit)
                ->all();

            //Populate each order by store and provider data
            foreach ($newOrders as $order) {
                $providerId = $order['provider_id'];

                $order['store_id'] = $storeId;
                $order['store_db'] = $store['db_name'];
                $order['provider_site'] = $storeProviders[$providerId]['site'];
                $order['provider_protocol'] = $storeProviders[$providerId]['protocol'];
                $order['provider_apikey'] = $storeProviders[$providerId]['apikey'];

                $orders[] = $order;
            }

            if (count($orders) >= $this->ordersLimit) {
                break;
            }
        }

        return $orders;
    }

    /**
     * Return Sommerce order status by Panel order status
     * @param $panelStatus string
     * @return int
     * @throws Exception
     */
    static function getSommerceStatusByPanelStatus($panelStatus)
    {
        $panelStatus = mb_strtolower($panelStatus);

        $statusMatching = [
            'pending' => Suborders::STATUS_PENDING,
            'in progress' => Suborders::STATUS_IN_PROGRESS,
            'partial' => Suborders::STATUS_ERROR,
            'canceled' => Suborders::STATUS_CANCELED,
            'processing' => Suborders::STATUS_IN_PROGRESS,
            'completed' => Suborders::STATUS_COMPLETED,
        ];

        $sommerceStatus = ArrayHelper::getValue($statusMatching, $panelStatus, null);

        if (is_null($sommerceStatus)) {
            throw new Exception('Unknown panel status ' . $panelStatus);
        }

        return $sommerceStatus;
    }

    /**
     * Update suborder by values
     * Only the values that are passed as not null in the $values will be updated.
     * If passed value is null, current field value will not changed.
     * @param $orderInfo
     * @param $values
     */
    private function _updateOrder($orderInfo, $values)
    {
        $orderId = $orderInfo['suborder_id'];
        $storeDb = $orderInfo['store_db'];
        $newStatus = ArrayHelper::getValue($values, ':status');
        $oldStatus = ArrayHelper::getValue($orderInfo, 'status');

        $defaultValues = [
            ':status' => null,
            ':provider_charge' => null,
            ':provider_response' => null,
            ':provider_response_code' => null,
            ':updated_at' => time(),
        ];

        $values = array_intersect_key(array_merge($defaultValues, $values), $defaultValues);

        $this->_db->createCommand("UPDATE $storeDb.$this->_tableSuborders
              SET 
              `status` = COALESCE(:status, `status`),
              `provider_charge` = COALESCE(:provider_charge, `provider_charge`),
              `provider_response` = COALESCE(:provider_response, `provider_response`),
              `provider_response_code` = COALESCE(:provider_response_code, `provider_response_code`),
              `updated_at` = :updated_at
              WHERE `id` = :id
            ")
            ->bindValues($values)
            ->bindValue(':id', $orderId)
            ->execute();

        if ($newStatus != $oldStatus) {
            if (Suborders::STATUS_ERROR == $newStatus) {
                // Event error order
                Events::add(Events::EVENT_STORE_ORDER_ERROR, [
                    'suborderId' => $orderInfo['suborder_id'],
                    'storeId' => $orderInfo['store_id']
                ]);
            }

            if (Suborders::STATUS_IN_PROGRESS == $newStatus) {
                // Event in progress order
                Events::add(Events::EVENT_STORE_ORDER_IN_PROGRESS, [
                    'suborderId' => $orderInfo['suborder_id'],
                    'storeId' => $orderInfo['store_id']
                ]);
            }

            if (Suborders::STATUS_COMPLETED == $newStatus) {
                // Event completed order
                Events::add(Events::EVENT_STORE_ORDER_COMPLETED, [
                    'suborderId' => $orderInfo['suborder_id'],
                    'storeId' => $orderInfo['store_id']
                ]);
            }
        }
    }

    /**
     * Change provider protocol from http to https
     * @param $providerId
     */
    private function _switchProviderProtocol($providerId)
    {
        Yii::$app->db
            ->createCommand("
              UPDATE $this->_tableProviders
              SET `protocol` = :protocol
              WHERE `id` = :id
            ")
            ->bindValues([
                ':protocol' => Providers::PROTOCOL_HTTPS,
                ':id' => $providerId,
            ])
            ->execute();
    }

    /**
     * Get provider order status
     * @return array
     */
    private function _getStatus()
    {
        $sendResults = [
            'total' => count($this->_orders),
            'success' => 0,

            'err_curl' => 0,
            'err_https' => 0,
            'err_http' => 0,
            'err_json' => 0,
            'err_other' => 0
        ];

        $mh = curl_multi_init();
        $connectionHandlers = [];

        /**
         * Make request pull
         */
        foreach ($this->_orders as $order) {

            $apiUrl = ($order['provider_protocol'] == Providers::PROTOCOL_HTTPS ? 'https://' : 'http://') . $order['provider_site'] . '/api/v2';

            $requestParams = array(
                'key' => $order['provider_apikey'],
                'action' => Providers::API_ACTION_STATUS,
                'order' => $order['provider_order_id'],
            );

            $curlOptions = array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_VERBOSE => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($requestParams),

                CURLOPT_PRIVATE => json_encode([
                    'store_id' => $order['store_id'],
                    'store_db' => $order['store_db'],
                    'suborder_id' => $order['id'],
                    'status' => $order['status'],
                    'provider_id' => $order['provider_id'],
                    'protocol' => $order['provider_protocol']
                ]),
            );

            $ch = curl_init();
            curl_setopt_array($ch, $curlOptions);
            curl_multi_add_handle($mh, $ch);
            $connectionHandlers[] = $ch;
        }

        /**
         * Make request pull
         */
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        /**
         * Process results
         */
        foreach ($connectionHandlers as $ch) {
            $orderInfo = json_decode(curl_getinfo($ch, CURLINFO_PRIVATE), true);
            $protocol = $orderInfo['protocol'];

            // System Errors
            if (curl_errno($ch)) {
                $error = json_encode(curl_error($ch));
                $values = [
                    ':status' => Suborders::STATUS_ERROR,
                    ':provider_response' => $error,
                    ':provider_response_code' => 0,
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
                    ':provider_response' => $responseRawResult,
                    ':provider_response_code' => $responseCode,
                ];

                $this->_updateOrder($orderInfo, $values);

                $sendResults['err_http']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // Protocol error
            $responseProtocol = parse_url($requestInfo['url'], PHP_URL_SCHEME);
            if (
                $protocol == Providers::PROTOCOL_HTTP &&
                ArrayHelper::getValue($responseResult, 'error') == 'Incorrect request' &&
                $responseProtocol == 'https'
            ) {
                $this->_switchProviderProtocol($orderInfo['provider_id']);

                $sendResults['err_https']++;

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // Success
            if (isset(
                $responseResult['status'],
                $responseResult['charge'])
            ) {
                $values = [
                    ':status' => static::getSommerceStatusByPanelStatus($responseResult['status']),
                    ':provider_charge' => $responseResult['charge'],
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