<?php

namespace console\controllers\my;


use common\models\panels\ReferralEarnings;
use common\models\panels\ReferralVisits;
use yii\db\Query;


/**
 * Class GenerateReferralsDataController
 * @package console\controllers\my
 */
class GenerateReferralsDataController extends CustomController
{

    public function actionGenerate()
    {
        $lastRefVisit = (new Query())
            ->select('id')
            ->from('referral_visits')
            ->orderBy('id DESC')
            ->one();

        $lastRefEarning = (new Query())
            ->select('id')
            ->from('referral_earnings')
            ->orderBy('id DESC')
            ->one();

        $lastCustomer = (new Query())
            ->select('id')
            ->from('customers')
            ->orderBy('id DESC')
            ->one();

        $lastInvoice = (new Query())
            ->select('id')
            ->from('invoices')
            ->orderBy('id DESC')
            ->one();

        for ($i = $lastRefVisit['id'] + 1; $i < $lastRefVisit['id'] + 2001; $i++) {
            $cust = rand(6277, $lastCustomer['id']);

            $visits = new ReferralVisits();
            $visits->id = $i;
            $visits->customer_id = $cust;
            $visits->ip = $this->getRandomIp();
            $visits->user_agent =  'IE-8';
            $visits->http_referer = '1';
            $visits->request_data = '123';
            $visits->created_at = time();
            $visits->insert();
            print_r($visits->errors);
        }

        for ($i = $lastRefEarning['id'] + 1; $i < $lastRefEarning['id'] + 2001; $i++) {
            $cust = rand(6277, $lastCustomer['id']);

            $earnings = new ReferralEarnings();
            $earnings->id = $i;
            $earnings->customer_id = $cust;
            $earnings->earnings = rand(1, 100);
            $earnings->status = rand(1, 4);
            $earnings->invoice_id = rand(0, $lastInvoice['id']);
            $earnings->created_at = time();
            $earnings->insert();
            print_r($earnings->errors);
        }
    }

    private function getRandomIp()
    {
        $ip1 = (string)rand(0, 222);
        $ip2 = (string)rand(0, 222);
        $ip3 = (string)rand(0, 222);
        $ip4 = (string)rand(0, 222);
        $resultIP = $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;

        return $resultIP;
    }
}