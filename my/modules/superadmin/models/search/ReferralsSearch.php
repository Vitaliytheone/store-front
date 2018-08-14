<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\Customers;
use common\models\panels\ReferralEarnings;
use yii\data\Pagination;
use yii\db\Query;

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

    public static $pageSizeList = [100, 500, 1000, 5000, 'all'];

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

    public function setPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        return in_array($pageSize, static::$pageSizeList) ? $pageSize : 100;
    }

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

    private function queryCount()
    {
        return $this->buildQuery()
            ->select([
                'customers.id',
                'customers.email',
                'COUNT(DISTINCT referral_visits.id) as total_visits',
            ])
            ->leftJoin('referral_visits', 'customers.id = referral_visits.customer_id')
            ->having('total_visits > 0')
            ->groupBy('customers.id')
            ->count();
    }

    /**
     * Build sql query
     * @return array
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
            'COUNT(DISTINCT referral_visits.id) as total_visits',
            'COUNT(DISTINCT IF(referrer.paid = 0, referrer.id, NULL)) as unpaid_referrals',
            'COUNT(DISTINCT IF(referrer.paid = 1, referrer.id, NULL)) as paid_referrals',
            're.total_earnings as total_earnings',
            'IF (unp.unpaid_earnings IS NULL, re.total_earnings, re.total_earnings - unp.unpaid_earnings) as unpaid_earnings',
        ]);
        $referrals->leftJoin('referral_visits', 'customers.id = referral_visits.customer_id');
        $referrals->leftJoin('(' . $referralEarningsSum->createCommand()->rawSql .') as re', 'customers.id = re.customer_id');
        $referrals->leftJoin('(' . $unpaidQuery->createCommand()->rawSql .') as unp', 'customers.id = unp.customer_id');
        $referrals->leftJoin('customers as referrer', 'customers.id = referrer.referrer_id');
        $referrals->having('total_visits > 0');

        return $referrals->orderBy([
                're.total_earnings' => SORT_DESC
            ])->groupBy('customers.id');
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
     * Search panels
     * @return array
     */
    public function search()
    {
        $pages = new Pagination(['totalCount' => $this->queryCount()]);
        $pages->setPageSize($this->setPageSize());
        $pages->defaultPageSize = static::$pageSizeList[0];

        $model = $this->getReferrals()
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return [
            'models' => $this->prepareReferralsData($model),
            'pages' => $pages,
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