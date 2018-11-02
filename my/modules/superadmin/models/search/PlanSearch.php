<?php

namespace superadmin\models\search;

use common\models\panels\Tariff;
use yii\db\ActiveQuery;

/**
 * Class PlanSearch
 * @package superadmin\models
 */
class PlanSearch extends Tariff
{
    private $params;

    public $rows;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Build main search query
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery()
    {
        $query = static::find();

        return $query;
    }

    /**
     * Search staffs
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $models = $query->orderBy([
                'id' => SORT_ASC
            ])
            ->all();

        return $models;
    }
}