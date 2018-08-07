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
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class CustomersEarningController
 * @package console\controllers\superadmin
 */
class CustomersEarningController extends CustomController
{

    public function actionCompare()
    {
        $unpaidEarnings = (new Query())
            ->select([
                'id',
                'unpaid_earnings',
            ])
            ->from('customers')
            ->all();

        $customers = ArrayHelper::index(Customers::find()->all(), 'id');

        foreach ($unpaidEarnings as $key => $value) {
            $unpaidByMethod = $customers[$value['id']]->getUnpaidEarnings();

            if ($value['unpaid_earnings'] != $unpaidByMethod) {
                $this->stderr('Incorrect value: ' .  print_r([
                    'id' => $value['id'],
                    'unpaid_earnings' => $value['unpaid_earnings'],
                    'counted_unpaid_earnings' => $unpaidByMethod
                ], 1) . "\n", Console::FG_YELLOW);
            }
        }
    }
}
