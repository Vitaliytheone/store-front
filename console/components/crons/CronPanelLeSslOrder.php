<?php

namespace console\components\crons;

use common\models\panels\Orders;
use common\models\panels\Params;
use common\models\panels\SslCertItem;
use Yii;
use common\models\panels\Project;
use console\components\crons\exceptions\CronException;
use my\helpers\CurlHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class CronPanelLeSslOrder
 * @package console\components\crons
 */
class CronPanelLeSslOrder extends CronBase
{
    public function run()
    {
        $this->stdout($this->cronTaskName() . ' started now', Console::FG_GREEN);

        $panels = Project::find()
            ->andWhere([
                'act' => Project::STATUS_ACTIVE,
                'ssl' => Project::SSL_MODE_OFF,
                'dns_status' => Project::DNS_STATUS_ALIEN
            ])
            ->all();


        $this->stdout('Total panel domains count (' . count($panels) . ')');

        /** @var Project $panel */

        foreach ($panels as $panel) {

            $allowCheck = false;

            $createdSec = time() - $panel->date;
            $lastCheckedSec = time() - $panel->dns_checked_at;

            $this->stdout('Current panel domain [' . $panel->domain . ']');
            $this->stdout('Elapsed time since panel creation, sec (' . $createdSec . ')');
            $this->stdout('Elapsed time since last panel nameservers check, sec (' . $lastCheckedSec . ')');

            switch (true) {
//                // 1-th hour — each 3 min
//                case $createdSec < 1 * 60 * 60 && $lastCheckedSec > 3 * 60:
//                    $allowCheck = true;
//                    break;
//                // 2-th hour - each 5 min
//                case $createdSec > 1 * 60 * 60 && $createdSec < 2 * 60 * 60 && $lastCheckedSec > 5 * 60:
//                    $allowCheck = true;
//                    break;
//                // 3-th hour - each 10 min
//                case $createdSec > 2 * 60 * 60 && $createdSec < 3 * 60 * 60 && $lastCheckedSec > 10 * 60:
//                    $allowCheck = true;
//                    break;
//                // 4-th hour - each 15 min
//                case $createdSec > 3 * 60 * 60 && $createdSec < 4 * 60 * 60 && $lastCheckedSec > 15 * 60:
//                    $allowCheck = true;
//                    break;
//                // 5-th - 12-th hour - each 30 min
//                case $createdSec > 4 * 60 * 60 && $createdSec < 12 * 60 * 60 && $lastCheckedSec > 30 * 60:
//                    $allowCheck = true;
//                    break;
//                // 13-th hour - 31 day - each 60 min
//                case $createdSec > 12 * 60 * 60 && $createdSec < 31 * 24 * 60 * 60 && $lastCheckedSec > 60 * 60:
//                    $allowCheck = true;
//                    break;

                // 1-й день после регистрации - раз в 30 мин
                case $createdSec < 1 * 24 * 60 * 60 && $lastCheckedSec > 30 * 60:
                    $allowCheck = true;
                    break;
                // 2-й - 31 день после регистрации - раз в 3 часа
                case $createdSec > 1 * 24 * 60 * 60 && $createdSec < 31 * 24 * 60 * 60 && $lastCheckedSec > 3 * 60 * 60:
                    $allowCheck = true;
                    break;
            }

            if (!$allowCheck) {
                $this->stdout('Nameservers checkout is not allowed now! [Skipped]', Console::FG_YELLOW);
                continue;
            }

            $this->stdout('Nameservers checkout started...');

            $panel->dns_checked_at = time();

            if (!$panel->save(false)) {
                throw new CronException('Cannot update panel [' . $panel->id . '] data!');
            }

            $requestParams = http_build_query([
                'apiKey' => Params::get(Params::CATEGORY_SERVICE, Params::CODE_WHOISXMLAPI, 'ssl'),
                'domainName' => $panel->domain,
                'outputFormat' => 'JSON',
            ]);

            $request = Yii::$app->params['whoisxmlapi']['api_url'] . '/?' . $requestParams;

            $response = CurlHelper::request($request);

            $this->stdout('NsLoockup raw response:');
            $this->stdout(print_r($response,1));

            if (!$response) {
                continue;
            }

            $response = json_decode($response, true);

            $this->stdout('NsLoockup json-decoded response:');
            $this->stdout(print_r($response,1));

            if (json_last_error()) {
                continue;
            }

            $panel->setWhoisLookup($response);

            if (!$panel->save(false)) {
                throw new CronException('Cannot update panel [' . $panel->id . '] data!');
            }

            if (!ArrayHelper::getValue($response, 'WhoisRecord')) {
                $this->stderror('Invalid api NsLoockup response! [Skipped]');
                continue;
            }

            // Check 2 places
            $whoisNameservers = ArrayHelper::getValue(
                $response,
                ['WhoisRecord', 'registryData', 'nameServers', 'hostNames'],
                ArrayHelper::getValue($response, ['WhoisRecord', 'nameServers', 'hostNames'], [])
            );

            $this->stdout('NsLoockup nameservers:');
            $this->stdout(print_r($whoisNameservers,1));

            if (!$whoisNameservers || !is_array($whoisNameservers)) {
                $this->stderror('Domain nameservers are not defined! [Skipped]');
                continue;
            }

            $panel->setNameservers($whoisNameservers);

            if (!$panel->save(false)) {
                throw new CronException('Cannot update panel [' . $panel->id . '] data!');
            }

            $configNameservers = array_values(array_filter(Yii::$app->params['ahnames.my.ns'], function($nameserver) { return !empty($nameserver); }));

            $this->stdout('Config-params nameservers:');
            $this->stdout(print_r($configNameservers,1));

            if (array_udiff($configNameservers, $whoisNameservers, 'strcasecmp')) {
                $this->stdout('Nameservers do not match! [Skipped]');
                continue;
            }

            $panel->dns_status = Project::DNS_STATUS_MINE;

            if (!$panel->save(false)) {
                throw new CronException('Cannot update panel [' . $panel->id . '] data!');
            }

            $sslItem = SslCertItem::findOne([
                'provider' => SslCertItem::PROVIDER_LETSENCRYPT,
                'product_id' => SslCertItem::PRODUCT_ID_LETSENCRYPT_BASE
            ]);

            if (!$sslItem) {
                throw new CronException('Cannot find SslItem!');
            }

            $order = new Orders();
            $order->cid = $panel->cid;
            $order->status = Orders::STATUS_PAID;
            $order->hide = Orders::HIDDEN_OFF;
            $order->processing = Orders::PROCESSING_NO;
            $order->domain = $panel->domain;
            $order->item = Orders::ITEM_OBTAIN_LE_SSL;
            $order->ip = '127.0.0.1';
            $order->setDetails([
                'pid' => $panel->id,
                'project_type' => Project::getProjectType(),
                'domain' => $panel->domain,
                'ssl_cert_item_id' => $sslItem->id
            ]);

            if (!$order->save(false)) {
                throw new CronException('Cannot create order!');
            }

            $this->stdout('Letsencrypt SSL-order has been created successfully!');
            $this->stdout(print_r($order->attributes,1));
        }

        $this->stdout($this->cronTaskName() . ' finished', Console::FG_GREEN);
    }
}