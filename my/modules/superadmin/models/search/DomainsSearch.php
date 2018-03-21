<?php
namespace my\modules\superadmin\models\search;

use Yii;
use common\models\panels\Domains;
use yii\helpers\ArrayHelper;

/**
 * Class DomainsSearch
 * @package my\modules\superadmin\models\search
 */
class DomainsSearch {

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
     * @return $this
     */
    public function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();
        $customerId = ArrayHelper::getValue($this->params, 'customer_id');

        $domains = Domains::find();

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

        $panels = $query->groupBy('domains.id')->all();

        return [
            'models' => $panels
        ];
    }

    /**
     * Get count panels by type
     * @param int $status
     * @return int
     */
    public function count($status = null)
    {
        $query = clone $this->buildQuery($status);

        return (int)$query->select('COUNT(*)')->scalar();
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => Yii::t('app/superadmin', 'domains.list.navs_all', [
                'count' => $this->count()
            ]),
            Domains::STATUS_OK => Yii::t('app/superadmin', 'domains.list.navs_ok', [
                'count' => $this->count(Domains::STATUS_OK)
            ]),
            Domains::STATUS_EXPIRED => Yii::t('app/superadmin', 'domains.list.navs_expired', [
                'count' => $this->count(Domains::STATUS_EXPIRED)
            ]),
        ];
    }
}