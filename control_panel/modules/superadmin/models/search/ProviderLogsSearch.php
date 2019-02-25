<?php
namespace superadmin\models\search;

use common\models\panels\ProviderSearchLog;
use common\models\panels\queries\ProviderSearchLogQuery;
use yii\data\Pagination;

/**
 * Class SearchLog
 * @package superadmin\models\search
 */
class ProviderLogsSearch
{
    use SearchTrait;

    protected $pageSize = 100;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
        ];
    }

    /**
     * Build sql query
     * @return ProviderSearchLogQuery
     */
    public function buildQuery()
    {
        $searchQuery = $this->getQuery();

        $logs = ProviderSearchLog::find();

        if (!empty($searchQuery)) {
            $logs->andFilterWhere([
                'or',
                ['=', 'panel_id', $searchQuery],
                ['=', 'admin_id', $searchQuery],
            ]);
        }

        return $logs;
    }

    /**
     * Search logs
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $logs = $query->offset($pages->offset)
            ->with('project')
            ->with('admin')
            ->limit($pages->limit)
            ->orderBy([
                '.id' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $logs,
            'pages' => $pages,
        ];
    }
}