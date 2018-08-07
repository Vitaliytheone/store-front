<?php
/**
 * Created by PhpStorm.
 * User: vitalij.z
 * Date: 01.08.2018
 * Time: 12:50
 */

namespace console\controllers\superadmin;


use common\models\panels\ReferralEarnings;
use console\controllers\my\CustomController;
use common\models\panels\Customers;
use yii\db\Query;

/**
 * Class CustomersEarningController
 * @package console\controllers\superadmin
 */
class CustomersEarningController extends CustomController
{

    public function actionCompare()
    {
        $completedEarnings = (new Query())
            ->select(['customer_id', 'SUM(earnings) as total'])
            ->from('referral_earnings')
            ->where(['status' => ReferralEarnings::STATUS_COMPLETED])
            ->groupBy('customer_id');

        $debitEarnings = (new Query())
            ->select(['customer_id', 'SUM(earnings) as debit'])
            ->from('referral_earnings')
            ->where(['status' => ReferralEarnings::STATUS_DEBIT])
            ->groupBy('customer_id');

        $unpaidEarnings = (new Query())
            ->select([
                'customers.id',
                'IF (unpaid_earnings IS NULL, 0, unpaid_earnings) as unpaid_earnings',
                'IF ((complete.total - debit.debit) IS NULL, 0, (complete.total - debit.debit)) as getUnpaidEarnings'
            ])
            ->from('customers')
            ->leftJoin('(' . $completedEarnings->createCommand()->rawSql .') as complete', 'customers.id = complete.customer_id')
            ->leftJoin('(' . $debitEarnings->createCommand()->rawSql .') as debit', 'customers.id = debit.customer_id')
            ->all();

        foreach ($unpaidEarnings as $key => $customer) {
            if ($customer['unpaid_earnings'] == $customer['getUnpaidEarnings']) {
                unset($unpaidEarnings[$key]);
            }
        }

        print_r($unpaidEarnings);
    }
}
