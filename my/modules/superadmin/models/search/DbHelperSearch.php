<?php

namespace my\modules\superadmin\models\search;
use yii\db\Query;

/**
 * Class DbHelperSearch
 * @package my\modules\superadmin\models\search
 */
class DbHelperSearch
{

    const DEFAULT_QUERY = 'UPDATE `db_name`.`services` SET `provider_id` = `res`, `provider_service_id` = `reid`, `provider_service_params` = `params`;';

    use SearchTrait;

    /**
     * Get query for textarea
     * @return string
     */
    public function getQueryForInput()
    {
        return isset($this->params['query']) ? $this->params['query'] : static::DEFAULT_QUERY;
    }

    /**
     * Get default query string
     * @param $dbNames array
     * @return array
     */
    public function getQueryString($dbNames)
    {
        $query = $this->getQueryForInput();
        $array = [];

        if (empty($dbNames)) {
            $array[] = $query;
            return $array;
        }

        foreach ($dbNames as $dbName) {
            $array[] = str_replace('db_name', $dbName['db_name'], $query);
        }

        return $array;
    }

    /**
     * Get query
     * @return array
     */
    private function buildQuery($params = null)
    {
        if ($params == null) {
            return [];
        }

        $models = [];

        if ($params == 'Panels') {
            $models = (new Query())
                ->select([
                    'db as db_name'
                ])
                ->from('project')
                ->where('db != ""')
                ->orderBy(['orders' => SORT_ASC])
                ->all();
        } elseif ($params == 'Stores') {
            $models = (new Query())
                ->select([
                    'db_name as db_name'
                ])
                ->from('`' . DB_STORES . '`.stores')
                ->where('db_name != ""')
                ->orderBy(['id' => SORT_ASC])
                ->all();
        }

        return $models;
    }

    /**
     * @return array
     */
    public function search()
    {
        $params = isset($this->params['db_name']) ? $this->params['db_name'] : null;
        $model = $this->buildQuery($params);

        return $this->getQueryString($model);
    }
}
