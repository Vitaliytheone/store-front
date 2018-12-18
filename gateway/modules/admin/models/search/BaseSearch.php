<?php

namespace admin\models\search;

use yii\data\Pagination;
use yii\helpers\ArrayHelper;

/**
 * Class BaseSearch
 * @package app\models\search
 */
abstract class BaseSearch
{
    const PAGE_SIZE = 100;

    protected $params;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    abstract public function search();

    /**
     * Get formatted pagination
     * @param Pagination $pagination
     * @return array
     */
    protected function getPages(Pagination $pagination)
    {
        $pageCount = $pagination->getPageCount();
        $currentPage = $pagination->getPage() + 1;
        $next = $currentPage + 1;
        $prev = $currentPage - 1;
        if ($prev < 0) {
            $prev = 0;
        }

        return [
            'count' => $pagination->totalCount,
            'current' => $currentPage,
            'prev' => $prev,
            'pages' => $pageCount,
            'next' => $next
        ];
    }

    /**
     * Get search query
     * @return mixed
     */
    public function getQuery()
    {
        $query = (string)ArrayHelper::getValue($this->params, 'search', '');
        $query = trim($query);
        return !empty($query) ? $query : null;
    }
}