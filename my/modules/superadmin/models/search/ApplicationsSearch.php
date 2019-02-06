<?php

namespace superadmin\models\search;

use common\models\panels\Params;
use yii\db\ActiveQuery;

/**
 * Class ApplicationsSearch
 * @package superadmin\models
 */
class ApplicationsSearch extends Params
{
    private $params;

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

        $query->andWhere([
            'category' => static::CATEGORY_SERVICE,
        ]);
        $query->andWhere([
            'code' => array_keys(Params::getServices()),
        ]);

        return $query;
    }

    /**
     * Search contents
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $models = $query
            ->select(['id', 'code'])
            ->orderBy([
                'position' => SORT_ASC,
            ])
            ->asArray()
            ->all();

        return $models;
    }
}