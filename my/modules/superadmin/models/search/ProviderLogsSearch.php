<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\queries\SearchProcessorQuery;
use common\models\panels\SearchProcessor;
use yii\data\Pagination;

/**
 * Class SearchLog
 * @package my\modules\superadmin\models\search
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
     * @return SearchProcessorQuery
     */
    public function buildQuery()
    {
        $searchQuery = $this->getQuery();

        $logs = SearchProcessor::find();

        if (!empty($searchQuery)) {
            $logs->andFilterWhere([
                'or',
                ['=', 'pid', $searchQuery],
                ['=', 'uid', $searchQuery],
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