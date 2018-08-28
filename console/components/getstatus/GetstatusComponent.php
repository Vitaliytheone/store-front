<?php
namespace console\components\getstatus;

use common\events\Events;
use common\models\panels\AdditionalServices;
use common\models\panels\Getstatus;
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
    const PROVIDER_ORDER_STATUS_PENDING = '0';
    const PROVIDER_ORDER_STATUS_IN_PROGRESS = '1';
    const PROVIDER_ORDER_STATUS_COMPLETED = '2';
    const PROVIDER_ORDER_STATUS_PARTIAL = '3';
    const PROVIDER_ORDER_STATUS_CANCELED = '4';
    const PROVIDER_ORDER_STATUS_PROCESSING = '5';
    const PROVIDER_ORDER_STATUS_FAIL = '6';
    const PROVIDER_ORDER_STATUS_ERROR = '7';
    
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

    /**
     * Getstatus table name
     * @var
     */
    private $_tableGetstatus;

    /** @inheritdoc */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->_tableStores = Stores::tableName();
        $this->_tableSuborders = Suborders::tableName();
        $this->_tableProviders = AdditionalServices::tableName();
        $this->_tableStoreProviders = StoreProviders::tableName();
        $this->_tableGetstatus = Getstatus::tableName();
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
        $params = [
            'allStores' => false,
            'expiry' => false,
            'limit' => true,
            'withGetstatus' => true
        ];
        $this->_orders = $this->_getOrders($params);
        return $this->_updateStatus();
    }

    /**
     * Fill getstatus
     */
    public function fillGetstatus()
    {
        $params = [
            'allStores' => true,
            'expiry' => true,
            'limit' => false,
            'withGetstatus' => false
        ];

        $this->_orders = $this->_getOrders($params);
        foreach ($this->_orders as $order) {
            $getstatus = new Getstatus();
            $getstatus->pid = $order['store_id'];
            $getstatus->oid = $order['id'];
            $getstatus->roid = $order['provider_order_id'];
            $getstatus->login = $order['provider_apikey'];
            $getstatus->res = $order['provider_id'];
            $getstatus->apikey = '';
            $getstatus->passwd = '';
            $getstatus->reid = $order['provider_service'];
            $getstatus->page_id = $order['link'];
            $getstatus->count = $order['overflow_quantity'];
            $getstatus->start_count = 0;
            $getstatus->status = $order['status'];
            $getstatus->type = Getstatus::TYPE_STORES_INTERNAL;
            $getstatus->save(false);
        }
    }

    /**
     * Return processing orders from all stores.
     * limited by $ordersLimit and created time
     * @param $params
     * @return array
     */
    private function _getOrders($params)
    {
        $query = (new Query())
            ->select(['id', 'db_name'])
            ->from($this->_tableStores);

        if (!$params['allStores']) {
            $query ->andWhere(['status' => Stores::STATUS_ACTIVE]);
        }

        $stores = $query->andWhere(['not', ['db_name' => null]])
            ->andWhere(['not', ['db_name' => '']])
            ->indexBy('id')
            ->all();

        $orders = [];

        // Get orders from all shops.
        //Total orders count limited by $ordersLimit
        foreach ($stores as $storeId => $store) {

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
            $query = (new Query());
            $selection = ['suborders.*'];

            if ($params['withGetstatus']) {
                $selection[] = 'getstatus.id as getstatus_id';
            }

            $query ->select($selection)->from([
                'suborders' => "$db.$this->_tableSuborders"
            ]);

            if ($params['withGetstatus']) {
                $query->innerJoin($this->_tableGetstatus,
                    $this->_tableGetstatus . '.oid = suborders.id and '
                    . $this->_tableGetstatus . '.pid = :store_id', [
                    ':store_id' => $store['id']
                ]);
            }

            $query->andWhere([
                    'suborders.mode' => Suborders::MODE_AUTO
                ])
                ->andWhere([
                    'suborders.status' => [
                        Suborders::STATUS_PENDING,
                        Suborders::STATUS_IN_PROGRESS,
                        Suborders::STATUS_ERROR,
                    ]
                ]);

            if ($params['expiry']) {
                $fromDate = time() - Yii::$app->params['cron.orderExpiry'] * 24 * 60 * 60;
                $query->andWhere(['>', 'suborders.updated_at', $fromDate]);
            }

            if ($params['limit']) {
                $requestLimit = $this->ordersLimit - count($orders);
                $query->orderBy(['suborders.updated_at' => SORT_ASC])
                    ->limit($requestLimit);
            }

            $newOrders = $query->all();


            //Populate each order by store and provider data
            foreach ($newOrders as $order) {
                $providerId = $order['provider_id'];
                $order['store_id'] = $storeId;
                $order['store_db'] = $store['db_name'];
                $order['provider_site'] = $storeProviders[$providerId]['site'];
                $order['provider_apikey'] = $storeProviders[$providerId]['apikey'];

                $orders[] = $order;
            }

            if ($params['limit'] && count($orders) >= $this->ordersLimit) {
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
            self::PROVIDER_ORDER_STATUS_PENDING => [
                'value' => Suborders::STATUS_PENDING,
                'title' => 'Pending'
            ],
            self::PROVIDER_ORDER_STATUS_IN_PROGRESS => [
                'value' => Suborders::STATUS_IN_PROGRESS,
                'title' => 'In Progress'
            ],
            self::PROVIDER_ORDER_STATUS_PROCESSING => [
                'value' => Suborders::STATUS_IN_PROGRESS,
                'title' => 'In Progress'
            ],
            self::PROVIDER_ORDER_STATUS_FAIL => [
                'value' => Suborders::STATUS_IN_PROGRESS,
                'title' => 'In Progress'
            ],
            self::PROVIDER_ORDER_STATUS_PARTIAL => [
                'value' => Suborders::STATUS_ERROR,
                'title' => 'Error'
            ],
            self::PROVIDER_ORDER_STATUS_CANCELED => [
                'value' => Suborders::STATUS_CANCELED,
                'title' => 'Canceled'
            ],
            self::PROVIDER_ORDER_STATUS_ERROR => [
                'value' => Suborders::STATUS_IN_PROGRESS,
                'title' => 'In Progress'
            ],
            self::PROVIDER_ORDER_STATUS_COMPLETED => [
                'value' => Suborders::STATUS_COMPLETED,
                'title' => 'Completed'
            ],
            '8' => [
                'value' => Suborders::STATUS_PENDING,
                'title' => 'Pending'
            ],
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
            $fromDate = time() - Yii::$app->params['cron.orderExpiry'] * 24 * 60 * 60;
            
            if ($fromDate > $order['updated_at']) {
                Getstatus::deleteAll(['id' => $order['getstatus_id']]);
                continue;
            }
            
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
                'status' => $this->_convertStatus($providerOrder['status'])['title'],
                'remains' => $providerOrder['result'],
                'currency' => $providerOrder['charge_currency']
            ];

            $values = [
                ':status' => $this->_convertStatus($providerOrder['status'])['value'],
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

            if ($providerOrder['status'] == self::PROVIDER_ORDER_STATUS_PARTIAL) {
                Getstatus::deleteAll(['id' => $order['getstatus_id']]);
            }
        }
    }
}