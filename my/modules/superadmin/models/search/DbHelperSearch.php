<?php

namespace my\modules\superadmin\models\search;

use yii\db\Query;
use Yii;

/**
 * Class DbHelperSearch
 * @package my\modules\superadmin\models\search
 */
class DbHelperSearch
{

    const SELECT_DEFAULT = 0;
    const SELECT_PANELS = 1;
    const SELECT_STORES = 2;

    use SearchTrait;

    /**
     * @return array
     */
    public function getSelectList(): array
    {
        return [
            static::SELECT_DEFAULT => Yii::t('app/superadmin', 'db_helper.select.select_source'),
            static::SELECT_PANELS => Yii::t('app/superadmin', 'db_helper.select.panels'),
            static::SELECT_STORES => Yii::t('app/superadmin', 'db_helper.select.stores'),
        ];
    }

    /**
     * Get query for textarea
     * @return string
     */
    public function getQueryForInput()
    {
        return isset($this->params['query']) ? $this->params['query'] : null;
    }

    /**
     * Get default query string
     * @param $dbNames array
     * @return string
     */
    public function getQueryString($dbNames)
    {
        $query = $this->getQueryForInput();
        $resultString = '';

        if (empty($dbNames)) {
            return null;
        }

        foreach ($dbNames as $dbName) {
            $resultString .= str_replace('db_name', $dbName['db_name'], $query);
            $resultString .= "\n";
        }

        return $resultString;
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

        if ($params == static::SELECT_PANELS) {
            $models = (new Query())
                ->select([
                    'db as db_name'
                ])
                ->from('project')
                ->where('db != ""')
                ->orderBy(['orders' => SORT_ASC])
                ->all();
        } elseif ($params == static::SELECT_STORES) {
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
     * @return string
     */
    public function search()
    {
        $params = isset($this->params['db_name']) ? $this->params['db_name'] : null;
        $model = $this->buildQuery($params);

        return $this->getQueryString($model);
    }
}
