<?php

namespace superadmin\models\search\dashboard;

use common\models\sommerces\Customers;
use common\models\sommerces\Domains as DomainsModel;
use control_panel\helpers\DomainsHelper;
use control_panel\helpers\SpecialCharsHelper;

/*
 * Source class for dashboard services
 */
class Domains extends BaseBlock
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
            DomainsModel::tableName() . '.id',
            'domain AS domain',
            Customers::tableName() . '.email AS customer',
            'created_at AS created',
            'expiry AS expired',
            DomainsModel::tableName() . '.status AS status'
        ]);

        $query->leftJoin(Customers::tableName(), DomainsModel::tableName() . '.customer_id = ' .Customers::tableName() . '.id');

        $query->orderBy("id DESC");
        $query->asArray();

        $items = $query->all();
        foreach ($items as &$item) {
            $item['domain'] = DomainsHelper::idnToUtf8($item['domain']);
            $item['status'] = DomainsModel::getStatuses()[$item['status']];
            $item['created'] = static::_formatDate($item['created']);
            $item['expired'] = static::_formatDate($item['expired']);
        }
        return SpecialCharsHelper::multiPurifier($items);
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
     * Domains constructor.
     */
    protected function __construct()
    {

    }


    /**
     * @return \yii\db\ActiveQuery
     */
    protected static function getQuery()
    {
        $query = DomainsModel::find();

        $query->where([
            '=',
            DomainsModel::tableName() . '.status', DomainsModel::STATUS_OK,
        ]);

        $query->andWhere([
            '>=',
            'created_at',
            static::_getFilterTime()
        ]);

        return $query;
    }
}