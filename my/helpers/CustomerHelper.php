<?php

namespace my\helpers;

use common\models\panels\Project;
use yii\db\Query;
use common\models\panels\Orders;
use common\models\stores\Stores;
use yii\db\Expression;

/**
 * Class CustomerHelper
 * @package my\helpers
 */
class CustomerHelper {

    /**
     * Return array of current stores and store orders
     * @param $customerId
     * @param $total boolean If true return total count, if false — return count 'pending', 'canceled', 'stores'
     * @return array | integer
     */
    public static function getCountStores($customerId, $total = false)
    {

        $orderPending = (new Query())
            ->select('COUNT(*)')
            ->from(Orders::tableName())
            ->andWhere([
                'cid' => $customerId,
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
            ->select('COUNT(*)')
            ->from(Orders::tableName())
            ->andWhere([
                'cid' => $customerId,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_STORE
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $stores = (new Query())
            ->select('COUNT(*)')
            ->from(Stores::tableName())
            ->andWhere([
                'customer_id' => $customerId,
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

        $counts = [
            'pending' => $orderPending->scalar(),
            'canceled' => $orderCanceled->scalar(),
            'stores' => $stores->scalar(),
        ];

        return $total ? array_sum($counts) : $counts;
    }


    /**
     * Return array of current panels and project orders
    * @param $total boolean If true return total count, if false — return count 'pending', 'canceled', 'panels'
     * @param $customerId
     * @return array | integer
     */
    public static function getCountPanels($customerId, $total = false)
    {
        $orderPending = (new Query())
            ->select('COUNT(*)')
            ->from(Orders::tableName())
            ->andWhere([
                'cid' => $customerId,
                'status' => [
                    Orders::STATUS_PAID,
                    Orders::STATUS_PENDING,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_PANEL,
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select('COUNT(*)')
            ->from(Orders::tableName())
            ->andWhere([
                'cid' => $customerId,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_PANEL
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $panels = (new Query())
            ->select('COUNT(*)')
            ->from(Project::tableName())
            ->andWhere([
                'cid' => $customerId,
                'act' => [
                    Project::STATUS_ACTIVE,
                    Project::STATUS_FROZEN,
                    Project::STATUS_TERMINATED,
                ],
            ])
            ->orderBy([
                new Expression('FIELD (act, ' . implode(',', [
                        Project::STATUS_ACTIVE,
                        Project::STATUS_FROZEN,
                        Project::STATUS_TERMINATED,
                    ]) . ')'),
                'id' => SORT_ASC
            ]);

        $counts = [
            'pending' => $orderPending->scalar(),
            'canceled' => $orderCanceled->scalar(),
            'panels' => $panels->scalar(),
        ];

        return $total ? array_sum($counts) : $counts;
    }

    /**
     * Return is customer have at least one store?
     * @param $customerId
     * @return bool
     */
    public static function hasStores($customerId)
    {
        return (bool)array_sum(static::getCountStores($customerId));
    }

    /**
     * Return is customer have at least one store?
     * @param $customerId
     * @return bool
     */
    public static function hasPanels($customerId)
    {
        return (bool)array_sum(static::getCountPanels($customerId));
    }
}