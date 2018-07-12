<?php

namespace my\models\search;

use common\models\panels\Tickets;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class TicketsSearch
 * @package my\models\search
 */
class TicketsSearch extends Tickets
{
    private $params;

    public $rows;

    public $pageSize = 50;

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

        $customer = ArrayHelper::getValue($this->params, 'customer_id');

        if ($customer) {
            $query->andWhere('customer_id = :customer_id', [':customer_id' => $customer]);
        }

        return $query;
    }

    /**
     * Get count tickets
     * @return int
     */
    public function count()
    {
        $query = clone $this->buildQuery();

        return (int)$query->select('COUNT(*)')->scalar();
    }

    /**
     * Search tickets
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $pages = new Pagination(['totalCount' => $this->count()]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $models = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'updated_at' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $models,
            'pages' => $pages
        ];
    }
}