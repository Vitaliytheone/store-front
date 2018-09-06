<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\Customers;
use common\models\panels\ReferralEarnings;
use my\modules\superadmin\widgets\CountPagination;
use yii\data\Pagination;
use yii\data\Sort;
use yii\db\Query;
use Yii;

/**
 * Class ReferralsSearch
 * @package my\modules\superadmin\models\search
 */
class ReferralsSearch extends ReferralEarnings
{

    /**
     * @var array
     */
    protected $_referrals = [];

    use SearchTrait;

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
     * @return int
     */
    public function setPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        if (isset($this->params['page_size']) && $this->params['page_size'] ==  'all') {
            return $this->queryCount();
        }
        return in_array($pageSize, CountPagination::$pageSizeList) ? $pageSize : 100;
    }

    /**
     * Build main query
     * @return Query
     */
    private function buildQuery()
    {
        $searchQuery = $this->getQuery();

        $referrals = (new Query())
            ->from('customers')
            ->andWhere([
                'customers.referral_status' => [
                    Customers::REFERRAL_ACTIVE,
                    Customers::REFERRAL_BLOCKED
                ]
            ]);

        if (!empty($searchQuery)) {
            $referrals->andFilterWhere([
                'or',
                ['=', 'customers.id', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        return $referrals;

    }

    /**
     * Build sql query
     * @return Query
     */
    public function _getReferrals()
    {
        $referrals = $this->buildQuery();

        $referralEarningsSum = (new Query())
            ->select([
                'customer_id',
                'SUM(earnings) as total_earnings'
            ])
            ->from('referral_earnings')
            ->where(['status' => ReferralEarnings::STATUS_COMPLETED])
            ->groupBy('customer_id');

        $unpaidQuery = (new Query())
            ->select([
                'customer_id',
                'SUM(earnings) as unpaid_earnings'
            ])
            ->from('referral_earnings')
            ->where(['status' => ReferralEarnings::STATUS_DEBIT])
            ->groupBy('customer_id');

        $referrals->select([
            'customers.id',
            'customers.email',
            'COUNT(DISTINCT IF(referrer.paid = 0, referrer.id, NULL)) as unpaid_referrals',
            'COUNT(DISTINCT IF(referrer.paid = 1, referrer.id, NULL)) as paid_referrals',
            're.total_earnings as total_earnings',
            'IF (unp.unpaid_earnings IS NULL, re.total_earnings, re.total_earnings - unp.unpaid_earnings) as unpaid_earnings',
        ]);
        $referrals->leftJoin('(' . $referralEarningsSum->createCommand()->rawSql .') as re', 'customers.id = re.customer_id');
        $referrals->leftJoin('(' . $unpaidQuery->createCommand()->rawSql .') as unp', 'customers.id = unp.customer_id');
        $referrals->leftJoin('customers as referrer', 'customers.id = referrer.referrer_id');

        return $referrals->groupBy('customers.id');
    }

    /**
     * Get panels
     * @return ReferralsSearch|array
     */
    public function getReferrals()
    {
        if (empty($this->_referrals)) {
            $this->_referrals = $this->_getReferrals();
        }

        return $this->_referrals;
    }

    /**
     * @param $referrals
     * @return Query
     */
    private function getTotalEarnings(): Query
    {
        $searchQuery = $this->getQuery();

        $earnings = (new Query())
            ->select(['COUNT(referral_visits.id) as total', 'customer_id'])
            ->from('referral_visits')
            ->leftJoin('customers', 'customers.id = referral_visits.customer_id')
            ->where([
                'customers.status' => [
                    Customers::REFERRAL_ACTIVE,
                    Customers::REFERRAL_BLOCKED
                ]
            ])
            ->groupBy('customer_id')
            ->having('total > 0');

        if (!empty($searchQuery)) {
            $earnings->andFilterWhere([
                'or',
                ['=', 'customers.id', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        return $earnings;
    }

    private function prepareData($referrals, $pages = null): array
    {
        $earnings = $this->getTotalEarnings()->indexBy('customer_id')->all();
        $result = [];

        foreach ($referrals as $key => $referral) {
            if (isset($earnings[$referral['id']])) {
                $result[$key] = $referral;
                $result[$key]['total_visits'] = $earnings[$referral['id']]['total'];
            }
        }

        if (isset($this->params['sort']) && $this->params['sort'] == 'total_visits') {
            usort($result, function($a, $b){
                return ($a['total_visits'] - $b['total_visits']);
            });
        } elseif (isset($this->params['sort']) && $this->params['sort'] == '-total_visits') {
            usort($result, function($a, $b){
                return -($a['total_visits'] - $b['total_visits']);
            });
        }

        return array_slice($result, $pages->offset, $pages->limit);
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $pages = new Pagination(['totalCount' => $this->getTotalEarnings()->count()]);
        $pages->setPageSize($this->setPageSize());
        $pages->defaultPageSize = CountPagination::$pageSizeList[100];

        $sort = new Sort([
            'attributes' => [
                'total_earnings' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.total_earnings'),
                ],
                'customers.id' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.customer_id'),
                ],
                'customers.email' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.customer_email'),
                ],
                'total_visits' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.total_visits'),
                ],
                'unpaid_referrals' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.unpaid_referrals'),
                ],
                'paid_referrals' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.paid_referrals'),
                ],
                'unpaid_earnings' => [
                    'label' => Yii::t('app/superadmin', 'referrals.list.unpaid_earnings'),
                ],
            ],
        ]);

        $sort->defaultOrder = [
            'total_earnings' => SORT_DESC,
        ];

        $model = $this->getReferrals()
            ->orderBy(isset($this->params['sort']) && trim($this->params['sort'], '-') == 'total_visits' ? null : $sort->orders)
            ->all();

        return [
            'models' => $this->prepareReferralsData($this->prepareData($model, $pages)),
            'pages' => $pages,
            'sort' => $sort,
        ];
    }

    /**
     * Get referrals
     * @param array $referrals
     * @return array
     */
    protected function prepareReferralsData($referrals)
    {
        $returnReferrals = [];

        foreach ($referrals as $key => $referral) {
            $totalVisits = $referral['total_visits'];
            $unpaidReferrals = $referral['unpaid_referrals'];
            $paidReferrals = $referral['paid_referrals'];
            $conversionRate = (($paidReferrals && $totalVisits) ? (($paidReferrals * 100) / $totalVisits) : 0);

            $returnReferrals[] = [
                'id' => $referral['id'],
                'email' => $referral['email'],
                'total_visits' => $totalVisits,
                'unpaid_referrals' => $unpaidReferrals,
                'paid_referrals' => $paidReferrals,
                'conversion_rate' => (is_float($conversionRate) ? number_format($conversionRate, 2) : $conversionRate) . '%',
                'total_earnings' => '$' . (number_format($referral['total_earnings'], 2)),
                'unpaid_earnings' => '$' . (number_format($referral['unpaid_earnings'], 2)),
            ];
        }

        return $returnReferrals;
    }
}