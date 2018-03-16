<?php
namespace my\modules\superadmin\models\search;

use yii\helpers\ArrayHelper;

/**
 * Class SearchTrait
 * @package my\modules\superadmin\models\search
 */
trait SearchTrait {

    protected $pageSize = 500;

    protected $params;

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
}