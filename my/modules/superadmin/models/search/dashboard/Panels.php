<?php

namespace superadmin\models\search\dashboard;

use common\models\panels\Customers;
use common\models\panels\Project;
use my\helpers\DomainsHelper;
use my\helpers\SpecialCharsHelper;

/*
 * Source class for dashboard services
 */
class Panels extends BaseBlock
{
    protected static $instance;

    /**
     * Get panels data
     * @return array
     */
    public static function getEntities()
    {
        $query = static::getQuery();
        $query->select([
            Project::tableName() . '.id',
            'site AS domain',
            Customers::tableName() . '.email AS customer',
            'date AS created',
            'expired AS expired',
            'act AS status'
        ]);
        $query->leftJoin(Customers::tableName(), Project::tableName() . '.cid = ' . Customers::tableName() . '.id');
        $query->orderBy("id DESC");
        $query->asArray();
        $items = $query->all();

        foreach ($items as &$item) {
            $item['domain'] = DomainsHelper::idnToUtf8($item['domain']);
            $item['status'] = Project::getStatuses()[$item['status']];
            $item['created'] = static::_formatDate($item['created']);
            $item['expired'] = static::_formatDate($item['expired']);
        }
        return SpecialCharsHelper::multiPurifier($items);
    }

    /**
     * Get count of panels
     * @return int
     */
    public static function getCount()
    {
        $query = static::getQuery();
        return $query->count();
    }

    /**
     * Panels constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected static function getQuery()
    {
        $query = Project::find();
        $query->where([
            '=',
            'act', Project::STATUS_ACTIVE,
        ]);

        $query->andWhere([
            '>=',
            'date',
            static::_getFilterTime()
        ]);

        $query->andWhere([
            '=',
            'child_panel',
            0
        ]);

        return $query;
    }
}