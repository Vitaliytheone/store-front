<?php

namespace control_panel\models\search;

use common\models\panels\Invoices;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class InvoicesSearch
 * @package control_panel\models\search
 */
class InvoicesSearch extends Invoices
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
            $query->andWhere('cid = :customer_id', [':customer_id' => $customer]);
        }

        return $query;
    }

    /**
     * Get count invoices
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
                'id' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $models,
            'pages' => $pages
        ];
    }
}