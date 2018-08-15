<?php

namespace my\modules\superadmin\models\search;
use yii\db\Query;

/**
 * Class DbHelperSearch
 * @package my\modules\superadmin\models\search
 */
class DbHelperSearch
{

    private static $query = 'UPDATE `db_name`.`services` SET `provider_id` = `res`, `provider_service_id` = `reid`, `provider_service_params` = `params`;';

    /**
     * Get default query string
     * @return string
     */
    public function getQueryString()
    {
        return static::$query;
    }

    /**
     * Get query
     * @return array
     */
    private function buildQuery()
    {
        $panels = (new Query())
            ->select([
                'db as panel'
            ])
            ->from('project')
            ->where('db != ""')
            ->all();

        $stores = (new Query())
            ->select([
                'db_name as store'
            ])
            ->from(DB_STORES . '.stores')
            ->where('db_name != ""')
            ->all();

        return [
            'panels' => $panels,
            'stores' => $stores
        ];
    }

    /**
     * @return array
     */
    public function search()
    {
        return $this->buildQuery();
    }
}
