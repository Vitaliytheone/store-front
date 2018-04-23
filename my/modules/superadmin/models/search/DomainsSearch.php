<?php
namespace my\modules\superadmin\models\search;

use Yii;
use common\models\panels\Domains;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class DomainsSearch
 * @package my\modules\superadmin\models\search
 */
class DomainsSearch extends Domains {

    protected $pageSize = 100;

    public $rows;

    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery()
        ];
    }

    /**
     * Build sql query
     * @param int $status
     * @return Query
     */
    public function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();
        $customerId = ArrayHelper::getValue($this->params, 'customer_id');

        $domains = static::find();

        $domains->joinWith(['customer']);

        if (null === $status || '' === $status) {
            if (empty($searchQuery)) {
                $domains->andWhere([
                    'domains.status' => [
                        Domains::STATUS_OK,
                        Domains::STATUS_EXPIRED,
                    ]
                ]);
            }
        } else {
            $domains->andWhere([
                'domains.status' => $status
            ]);
        }

        if (!empty($searchQuery)) {
            $domains->andFilterWhere([
                'or',
                ['=', 'domains.id', $searchQuery],
                ['like', 'domains.domain', $searchQuery],
            ]);
        }

        if ($customerId) {
            $domains->andWhere([
                'domains.customer_id' => $customerId
            ]);
        }

        $domains->orderBy([
            'domains.id' => SORT_DESC
        ]);

        return $domains;
    }

    /**
     * Search domains
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status');

        $query = clone $this->buildQuery($status);

        $pages = new Pagination(['totalCount' => $this->count($status)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $domains = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->groupBy('domains.id');

        return [
            'models' => static::queryAllCache($domains),
            'pages' => $pages
        ];
    }

    /**
     * Get count panels by type
     * @param int $status
     * @param array $filters
     * @return int|array
     */
    public function count($status = null, $filters = [])
    {
        $query = clone $this->buildQuery($status);

        if (!empty($filters['group']['status'])) {
            $query->select([
                'domains.status as status',
                'COUNT(DISTINCT domains.id) as rows'
            ])->groupBy('domains.status');

            return ArrayHelper::map(static::queryAllCache($query), 'status', 'rows');
        }

        $query->select('COUNT(DISTINCT domains.id)');

        return (int)static::queryScalarCache($query);
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        $statusCounters = $this->count(null, [
            'group' => [
                'status' => 1
            ],
        ]);

        return [
            null => Yii::t('app/superadmin', 'domains.list.navs_all', [
                'count' => $this->count()
            ]),
            Domains::STATUS_OK => Yii::t('app/superadmin', 'domains.list.navs_ok', [
                'count' => ArrayHelper::getValue($statusCounters, Domains::STATUS_OK, 0)
            ]),
            Domains::STATUS_EXPIRED => Yii::t('app/superadmin', 'domains.list.navs_expired', [
                'count' => ArrayHelper::getValue($statusCounters, Domains::STATUS_EXPIRED, 0)
            ]),
        ];
    }
}