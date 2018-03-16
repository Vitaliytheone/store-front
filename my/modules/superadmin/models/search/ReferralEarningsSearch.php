<?php
namespace my\modules\superadmin\models\search;

use my\helpers\DomainsHelper;
use common\models\panels\InvoiceDetails;
use common\models\panels\ReferralEarnings;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ReferralEarningsSearch
 * @package my\modules\superadmin\models\search
 */
class ReferralEarningsSearch {

    /**
     * @var array
     */
    protected $_referralEarnings = [];

    use SearchTrait;

    /**
     * Build sql query
     * @return array
     */
    public function _getReferralEarnings()
    {
        $referral = ArrayHelper::getValue($this->params, 'referral');

        $referralEarnings = (new Query())
            ->from('referral_earnings')
            ->andWhere([
                'referral_earnings.customer_id' => $referral
            ]);

        $referralEarnings->select([
            'referral_earnings.id',
            'referral_earnings.earnings',
            'referral_earnings.invoice_id',
            'referral_earnings.status',
            'referral_earnings.created_at',
            'COALESCE (project.site, orders.domain) as site',
        ]);

        $referralEarnings->leftJoin('invoice_details', 'invoice_details.invoice_id = referral_earnings.invoice_id AND item IN (' . implode(",", [
            InvoiceDetails::ITEM_BUY_PANEL,
            InvoiceDetails::ITEM_PROLONGATION_PANEL
        ]).')');
        $referralEarnings->leftJoin('orders', 'orders.id = invoice_details.item_id AND invoice_details.item = ' . InvoiceDetails::ITEM_BUY_PANEL);
        $referralEarnings->leftJoin('project', 'project.id = invoice_details.item_id AND invoice_details.item = ' . InvoiceDetails::ITEM_PROLONGATION_PANEL);

        return $referralEarnings->orderBy([
                'referral_earnings.id' => SORT_DESC
            ])->groupBy('referral_earnings.id')
            ->all();
    }

    /**
     * Get referral earnings
     * @return ReferralsSearch|array
     */
    public function getReferrals()
    {
        if (empty($this->_referralEarnings)) {
            $this->_referralEarnings = $this->_getReferralEarnings();
        }

        return $this->_referralEarnings;
    }

    /**
     * Search referral earnings
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
     * @param array $referralEarnings
     * @return array
     */
    protected function prepareReferralsData($referralEarnings)
    {
        $returnReferralEarnings = [];

        foreach ($referralEarnings as $referralEarning) {
            $returnReferralEarnings[] = [
                'id' => $referralEarning['id'],
                'earnings' => '$' . number_format($referralEarning['earnings'], 2),
                'invoice_id' => $referralEarning['invoice_id'],
                'status' => ReferralEarnings::getStatusNameString((int)$referralEarning['status']),
                'created_at' => ReferralEarnings::formatDate($referralEarning['created_at']),
                'site' => DomainsHelper::idnToUtf8($referralEarning['site']),
            ];
        }

        return $returnReferralEarnings;
    }
}