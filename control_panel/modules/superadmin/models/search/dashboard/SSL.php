<?php

namespace superadmin\models\search\dashboard;

use common\models\sommerces\Customers;
use common\models\sommerces\SslCert;
use control_panel\helpers\SpecialCharsHelper;

/*
 * Source class for dashboard services
 */
class SSL extends  BaseBlock
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
            SslCert::tableName() . '.id',
            'domain AS domain',
            Customers::tableName() . '.email AS customer',
            'created_at AS created',
            'expiry AS expired',
            'expiry_at_timestamp',
            SslCert::tableName() . '.status AS status'
        ]);

        $query->leftJoin(Customers::tableName(), SslCert::tableName() . '.cid = ' . Customers::tableName() . '.id');
        $query->orderBy("id DESC");
        $query->asArray();
        $items = $query->all();
        foreach ($items as &$item) {
            $item['status'] = SslCert::getStatuses()[$item['status']];
            $item['created'] = static::_formatDate($item['created']);
            $item['expired'] = isset($item['expired']) ? static::_formatDate($item['expired']) : static::_formatDate($item['expiry_at_timestamp']);
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
     * SSL constructor.
     */
    protected function __construct()
    {

    }


    /**
     * @return \yii\db\ActiveQuery
     */
    protected static function getQuery()
    {
        $query = SslCert::find();
        $query->where([
            '=',
            SslCert::tableName() . '.status', SslCert::STATUS_ACTIVE,
        ]);
        $query->andWhere([
            '>=',
            'created_at',
            static::_getFilterTime()
        ]);

        return $query;
    }
}