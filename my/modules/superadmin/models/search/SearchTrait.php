<?php
namespace my\modules\superadmin\models\search;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SearchTrait
 * @package my\modules\superadmin\models\search
 */
trait SearchTrait {

    protected $params;

    /**
     * @var bool
     */
    protected static $enableCache = false;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Get search query
     * @return mixed
     */
    public function getQuery()
    {
        $query = (string)ArrayHelper::getValue($this->params, 'query', '');
        $query = trim($query);
        return !empty($query) ? $query : null;
    }

    /**
     * Run all query
     * @param Query $query
     * @param int $duration
     * @return array
     */
    public static function queryAllCache(Query $query, $duration = 60)
    {
        if (!static::$enableCache) {
            return $query->all();
        }

        return Yii::$app->db->cache(function(Connection $db) use ($query) {
            return $query->all();
        }, $duration);
    }

    /**
     * Run one query
     * @param Query $query
     * @param int $duration
     * @return array
     */
    public static function queryOneCache(Query $query, $duration = 60)
    {
        if (!static::$enableCache) {
            return $query->one();
        }

        return Yii::$app->db->cache(function(Connection $db) use ($query) {
            return $query->one();
        }, $duration);
    }

    /**
     * Run scalar query
     * @param Query $query
     * @param int $duration
     * @return integer
     */
    public static function queryScalarCache(Query $query, $duration = 60)
    {
        if (!static::$enableCache) {
            return $query->scalar();
        }

        return Yii::$app->db->cache(function(Connection $db) use ($query) {
            return $query->scalar();
        }, $duration);
    }
}