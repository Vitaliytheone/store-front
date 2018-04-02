<?php

namespace my\helpers;

use Yii;
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
     * @return array
     */
    public static function getStores($customerId) {

        $orderPending = (new Query())
            ->select('COUNT(*)')
            ->from('orders')
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
            ->from('orders')
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

        return [
            'pending' => $orderPending->scalar(),
            'canceled' => $orderCanceled->scalar(),
            'stores' => $stores->scalar(),
        ];
    }

    /**
     * Return is customer have at least one store?
     * @param $customerId
     * @return bool
     */
    public static function hasStores($customerId)
    {
        $count = static::getStores($customerId);

        return (bool)array_sum(static::getStores($customerId));
    }
}