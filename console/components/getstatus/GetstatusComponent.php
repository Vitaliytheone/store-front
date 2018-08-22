<?php
namespace console\components\getstatus;

use common\events\Events;
use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use common\models\store\Suborders;
use common\models\stores\StoreProviders;
use common\models\stores\Stores;
use Yii;
use yii\base\Component;
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
        $this->_tableProviders = AdditionalServices::tableName();
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
     */
    public function run()
    {
        $this->_orders = $this->_getOrders();

        return $this->_updateStatus();
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
                    'site' => 'pr.name',
                    'type' => 'pr.type',
                    'apikey' => 'sp.apikey',
                ])
                ->from(['sp' => $this->_tableStoreProviders])
                ->leftJoin(['pr' => $this->_tableProviders], 'pr.res = sp.provider_id')
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

        if (null !== $newStatus && ($newStatus != $oldStatus)) {
            if (in_array($newStatus, [
                Suborders::STATUS_IN_PROGRESS,
                Suborders::STATUS_COMPLETED,
            ])) {
                Events::add(Events::EVENT_STORE_ORDER_CHANGED_STATUS, [
                    'suborderId' => $orderInfo['suborder_id'],
                    'storeId' => $orderInfo['store_id'],
                    'status' => $newStatus
                ]);
            }
        }



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
    }


    /**
     * Get sommerce status from panel
     * @param int|string $panelStatus
     * @return int
     */
    private function _convertStatus($panelStatus)
    {
        $statuses = [
            '0' =>
                [
                    'value' => Suborders::STATUS_PENDING, 
                    'title' => 'Pending'
                ],
            '8' => [
                'value' => Suborders::STATUS_PENDING,
                'title' => 'Pending'
            ],
            '1' => Suborders::STATUS_IN_PROGRESS,
            '3' => Suborders::STATUS_ERROR,
            '4' => Suborders::STATUS_CANCELED,
            '5' => Suborders::STATUS_IN_PROGRESS,
            '6' => Suborders::STATUS_IN_PROGRESS,
            '7' => Suborders::STATUS_IN_PROGRESS,
            '2' => Suborders::STATUS_COMPLETED
        ];

        return $statuses[(string)$panelStatus];

    }


    /**
     * Update provider order status
     */
    private function _updateStatus()
    {
        /**
         * Make request pull
         */
        foreach ($this->_orders as $order) {
            $panel_db = (new Query())->select('db')
                ->from(Project::tableName())
                ->where(['site' => $order['provider_site']])
                ->one()['db'];

            $providerOrder = (new Query())->select(['status', 'charge', 'start_count', 'charge_currency', 'result'])
                ->from($panel_db . '.orders')
                ->where(['id' => $order['provider_order_id']])->one();



            $response = [
                'charge' => $providerOrder['charge'],
                'start_count' => $providerOrder['start_count'],
                'status' => Suborders::getStatusName(
                    $this->_convertStatus($providerOrder['status']),
                    false
                ),
                'remains' => $providerOrder['result'],
                'currency' => $providerOrder['charge_currency']
            ];

            echo json_encode($response)."\n";
            echo Yii::t('admin', 'orders.filter_status_pending');

            $values = [
                ':status' => $this->_convertStatus($providerOrder['status']),
                ':provider_charge' => $providerOrder['charge'],
                ':provider_response' =>  json_encode($response),
                ':provider_response_code' => '200'
            ];

            $orderInfo = [
                'store_id' => $order['store_id'],
                'store_db' => $order['store_db'],
                'suborder_id' => $order['id'],
                'status' => $order['status'],
                'provider_id' => $order['provider_id'],
            ];

            $this->_updateOrder($orderInfo, $values);
        }
    }
}