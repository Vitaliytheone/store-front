<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\Customers;
use common\models\panels\Payments;
use yii\db\Query;

/**
 * Class ReferralsPaymentsSearch
 * @package my\modules\superadmin\models\search
 */
class ReferralsPaymentsSearch {

    /**
     * @var array
     */
    protected $_referrals = [];

    /**
     * @var Customers
     */
    protected $_customer;

    protected $_params = [];

    public function setParams($params)
    {
        $this->_params = $params;
    }

    /**
     * Build sql query
     * @return array
     */
    public function _getReferrals()
    {
        $referrals = (new Query())
            ->from('customers')
            ->andWhere([
                'customers.referrer_id' => $this->_customer->id
            ]);

        if (isset($this->_params['paid'])) {
            $referrals->andWhere([
                'customers.paid' => $this->_params['paid']
            ]);
        }

        $referrals->select([
            'customers.id',
            'customers.email',
            'customers.date_create',
            'SUM(payments.amount) as paid'
        ]);
        $referrals->leftJoin('invoices', 'invoices.cid = customers.id');
        $referrals->leftJoin('payments', 'payments.iid = invoices.id AND payments.status = :paymentsStatus', [
            ':paymentsStatus' => Payments::STATUS_COMPLETED
        ]);

        return $referrals->orderBy([
            'customers.id' => SORT_DESC
        ])->groupBy('customers.id')
            ->all();
    }

    /**
     * Set customer
     * @param $customer
     */
    public function setCustomer($customer)
    {
        $this->_customer = $customer;
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
        return [
            'models' => $this->prepareReferralsData($this->getReferrals())
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

        foreach ($referrals as $referral) {
            $returnReferrals[] = [
                'id' => $referral['id'],
                'email' => $referral['email'],
                'date_create' => Customers::formatDate($referral['date_create']),
                'paid' => '$' . (number_format((float)$referral['paid'], 2)),
            ];
        }

        return $returnReferrals;
    }
}