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
        $id = ArrayHelper::getValue($this->params, 'id');

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
        if ($id) {
            $domains->andWhere([
                'domains.id' => $id
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

        $pages = new Pagination(['totalCount' => $query->count()]);
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
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => Yii::t('app/superadmin', 'domains.list.navs_all', [
                'count' => $this->buildQuery()->count()
            ]),
            Domains::STATUS_OK => Yii::t('app/superadmin', 'domains.list.navs_ok', [
                'count' => $this->buildQuery(Domains::STATUS_OK)->count()
            ]),
            Domains::STATUS_EXPIRED => Yii::t('app/superadmin', 'domains.list.navs_expired', [
                'count' => $this->buildQuery(Domains::STATUS_EXPIRED)->count()
            ]),
        ];
    }
}