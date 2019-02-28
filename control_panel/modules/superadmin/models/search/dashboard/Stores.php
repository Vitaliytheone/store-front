<?php

namespace superadmin\models\search\dashboard;

use common\models\sommerces\Customers;
use common\models\sommerces\Stores as StoresModel;
use control_panel\helpers\DomainsHelper;
use control_panel\helpers\SpecialCharsHelper;

/*
 * Source class for dashboard services
 */
class Stores extends  BaseBlock
{
    protected static $instance;

    /**
     * Get entities data
     * @return array
     */
    public static function getEntities()
    {
        $query = static::getQuery();
        $query->select([
            StoresModel::tableName() . '.id',
            'domain AS domain',
            'customer_id AS customer',
            'created_at AS created',
            'expired AS expired',
            StoresModel::tableName() . '.status AS status'
        ]);

        $query->orderBy("id DESC");
        $query->asArray();
        $stores = $query->all();

        $customers = array();

        foreach ($stores as $store) {
            $customers[] = $store['customer'];
        }

        $queryCustomer = Customers::find();
        if (count($customers) > 0) {
            $query->where([
                'id' => $customers
            ]);
        }

        $queryCustomer->asArray();
        $queryCustomer->indexBy('id');
        $customers = $queryCustomer->all();

        foreach ($stores as &$store) {
            $store['domain'] = DomainsHelper::idnToUtf8($store['domain']);
            $store['customer'] = $customers[$store['customer']]['email'];
            $store['status'] = StoresModel::getStatuses()[$store['status']];
            $store['created'] = static::_formatDate($store['created']);
            $store['expired'] = static::_formatDate($store['expired']);
        }
        return SpecialCharsHelper::multiPurifier($stores);
    }

    /**
     * Get count of entities
     * @return int
     */
    public static function getCount()
    {
        $query = static::getQuery();
        return $query->count();
    }

    /**
     * Stores constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected static function getQuery()
    {
        $query = StoresModel::find();

        $query->where([
            '=',
            'status', StoresModel::STATUS_ACTIVE,
        ]);

        $query->andWhere([
            '>=',
            'created_at',
            static::_getFilterTime()
        ]);

        return $query;
    }
}