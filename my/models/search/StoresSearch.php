<?php

namespace my\models\search;

use common\models\stores\StoreDomains;
use my\helpers\DomainsHelper;
use Yii;
use common\models\panels\Orders;
use common\models\stores\Stores;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

/**
 * Class StoresSearch
 * @package my\models\search
 */
class StoresSearch
{
    /** @var  array Search params */
    private $params;

    /**
     * @var array
     */
    public static $storeDomains;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Build sql query
     * @return array
     */
    public function buildQuery()
    {
        $customer = ArrayHelper::getValue($this->params, 'customer_id');

        $orderPending = (new Query())
            ->select(['id', '("order") AS type', '("' . $customer . '") as customer_id', 'domain AS domain', 'status', 'date', '(NULL) AS plan', '(NULL) AS expired', '(NULL) AS db'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => [
                    Orders::STATUS_PAID,
                    Orders::STATUS_PENDING,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_STORE,
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select(['id', '("order") AS type', '("' . $customer . '") as customer_id', 'domain AS domain', 'status', 'date', '(NULL) AS plan', '(NULL) AS expired', '(NULL) AS db'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_STORE
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $stores = (new Query())
            ->select(['id', '("store") AS type', '("' . $customer . '") as customer_id', 'domain AS domain', 'status', 'created_at AS date', '(NULL) AS plan', 'expired', 'db_name AS db'])
            ->from(Stores::tableName())
            ->andWhere([
                'customer_id' => $customer,
                'status' => [
                    Stores::STATUS_ACTIVE,
                    Stores::STATUS_FROZEN,
                    Stores::STATUS_TERMINATED,
                ],
            ])
            ->orderBy([
                new Expression('FIELD (status, ' . implode(',', [
                        Stores::STATUS_ACTIVE,
                        Stores::STATUS_FROZEN,
                        Stores::STATUS_TERMINATED,
                    ]) . ')'),
                'id' => SORT_ASC
            ]);

        return [
            'pending' => $orderPending,
            'canceled' => $orderCanceled,
            'stores' => $stores,
        ];
    }

    /**
     * Search stores
     * @return array
     */
    public function search()
    {
        $customer = ArrayHelper::getValue($this->params, 'customer');
        $timezone = null;
        if (!Yii::$app->user->isGuest) {
            $timezone = Yii::$app->user->identity->timezone;
        }

        $errorsOrders = ArrayHelper::getColumn(Orders::find()->andWhere([
            'status' => Orders::STATUS_ERROR,
            'item' => Orders::ITEM_BUY_STORE
        ])->all(), 'domain');

        $ordersStatuses = Orders::getStatuses();
        $storesStatuses = Stores::getStatuses();
        $storeDomains = [];

        $prepareData = function ($value, $code) use ($customer, $ordersStatuses, $storesStatuses, $timezone, $errorsOrders, &$storeDomains) {
            $access = [
                'isActive' => false,
                'isActivityLog' => false,
                'isDomainConnect' => false,
                'isProlong' => false,
                'store_domain' => null
            ];
            if ('pending' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_PENDING];
            } else if ('canceled' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_CANCELED];
            } else {
                $storeDomain = !empty($storeDomains[$value['id']]) ? $storeDomains[$value['id']] : null;

                // skip stores with errors
                if (in_array($value['domain'], $errorsOrders)) {
                    return null;
                }
                $value['statusName'] = $storesStatuses[$value['status']];
                $value['expired'] = Yii::$app->formatter->asDate($value['expired'] + ((int)$timezone) + Yii::$app->params['time'], 'php:Y-m-d H:i:s');

                $access['canDashboard'] = Stores::hasAccess($value, Stores::CAN_DASHBOARD);
                $access['canDomainConnect'] = Stores::hasAccess($value, Stores::CAN_DOMAIN_CONNECT, [
                    'customer' => $customer,
                    'last_update' => ArrayHelper::getValue($storeDomain, 'updated_at')
                ]);
                $access['canProlong'] = Stores::hasAccess($value, Stores::CAN_PROLONG);
                $access['canActivityLog'] = Stores::hasAccess($value, Stores::CAN_ACTIVITY_LOG);

                $value['store_domain'] = ArrayHelper::getValue($storeDomain, 'domain');
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone) + Yii::$app->params['time'], 'php:Y-m-d H:i:s');
            $value['domain'] = DomainsHelper::idnToUtf8($value['domain']);
            $value['access'] = $access;

            return $value;
        };

        $queries = $this->buildQuery();

        $return = [];

        foreach ($queries as $code => $query) {
            $data = $query->all();

            if ('stores' == $code) {
                $storeDomains = static::getStoreDomains(ArrayHelper::getColumn($data, 'id'));
            }
            foreach ($data as $value) {
                $value = $prepareData($value, $code);

                if (!empty($value)) {
                    $return[] = $value;
                }
            }
        }

        return $return;
    }

    /**
     * Get sores domains
     * @param array $storeIds
     * @return array
     */
    public static function getStoreDomains($storeIds)
    {
        if (null === static::$storeDomains) {
            static::$storeDomains = (new Query())
                ->select([
                    'store_id',
                    'domain',
                    'type',
                    'updated_at'
                ])
                ->from(StoreDomains::tableName())
                ->andWhere([
                    'type' => [
                        StoreDomains::DOMAIN_TYPE_DEFAULT,
                        StoreDomains::DOMAIN_TYPE_SUBDOMAIN
                    ],
                    'store_id' => $storeIds
                ])
                ->all();

            static::$storeDomains = ArrayHelper::index(static::$storeDomains, 'store_id');
        }

        return static::$storeDomains;
    }
}