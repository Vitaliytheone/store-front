<?php

namespace console\components\crons;

use Yii;
use common\models\common\ProjectInterface;
use common\models\panels\Orders;
use common\models\panels\SslCertItem;
use common\models\stores\Stores;
use console\components\dns_checker\DnsCheckerPhp;
use common\models\panels\Project;
use console\components\crons\exceptions\CronException;
use yii\base\Exception;
use yii\helpers\Console;

/**
 * Class CronPanelLeSslOrder
 * @package console\components\crons
 */
class CronFreeSslOrder extends CronBase
{

    /** @inheritdoc */
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

        $stores = Stores::find()
            ->andWhere([
                'status' => Stores::STATUS_ACTIVE,
                'ssl' => Stores::SSL_MODE_OFF,
                'dns_status' => Stores::DNS_STATUS_ALIEN
            ])
            ->all();

        $this->stdout('Total panels`s domains count (' . count($panels) . ')');
        $this->stdout('Total stores`s domains count (' . count($stores) . ')');

        foreach ([$panels, $stores] as $projectList) {

            /** @var Project|Stores $project */
            foreach ($projectList as $project) {

                if (!$project instanceof ProjectInterface) {
                    continue;
                }

                $this->stdout('Current project [' . $project::getProjectType() . '] domain [' . $project->domain . ']');

                // Чекаем раз в 5 минут вне зависомости от даты регистрации
                if (time() - (int)$project->dns_checked_at < 5 * 60) {

                    $this->stdout('Project checkout is not allowed now! [Skipped]', Console::FG_YELLOW);

                    continue;
                }

                $project->dns_checked_at = time();

                if (!$project->save(false)) {
                    throw new CronException('Cannot update project [' . $project::getProjectType() . '|' . $project->domain . '] data!');
                }

                $dnsChecker = new DnsCheckerPhp();
                $dnsChecker->setFlushCache(true);
                $dnsChecker->setDomain($project->domain);
                $dnsChecker->setSubdomain((bool)$project->subdomain);

                $checkResult = $dnsChecker->check();

                $project->setWhoisLookup($dnsChecker->getDnsRecords());
                $project->setNameservers($dnsChecker->getDnsCheckoutRecord());

                if (!$project->save(false)) {
                    throw new CronException('Cannot update project [' . $project::getProjectType() . '|' . $project->domain . '] data!');
                }

                if (!$checkResult || $dnsChecker->getErrors())
                {
                    $this->stdout('DNS check not passed! Project [' . $project::getProjectType() . '|' . $project->domain . ']! [ skipped ]');

                    if ($dnsChecker->getErrors()) {
                        $this->stdout('DNS checker errors: ' . PHP_EOL . print_r($dnsChecker->getErrors(), 1));
                    }

                    continue;
                }

                $this->stdout('DNS checkout record: ' . PHP_EOL . print_r($dnsChecker->getDnsCheckoutRecord(), 1));

                $project->dns_status = ProjectInterface::DNS_STATUS_MINE;

                if (!$project->save(false)) {
                    throw new CronException('Cannot update project [' . $project::getProjectType() . '|' . $project->domain . '] data!');
                }

                $sslItem = SslCertItem::findOne([
                    'provider' => SslCertItem::PROVIDER_LETSENCRYPT,
                    'product_id' => SslCertItem::PRODUCT_ID_LETSENCRYPT_BASE
                ]);

                if (!$sslItem) {
                    throw new CronException('Cannot find SslItem!');
                }

                // Create SSL order for store or panel

                switch ($project::getProjectType()) {

                    case ProjectInterface::PROJECT_TYPE_PANEL:

                        $order = new Orders();
                        $order->cid = $project->cid;
                        $order->status = Orders::STATUS_PAID;
                        $order->hide = Orders::HIDDEN_OFF;
                        $order->processing = Orders::PROCESSING_NO;
                        $order->domain = $project->domain;
                        $order->item = Orders::ITEM_FREE_SSL;
                        $order->ip = '127.0.0.1';
                        $order->setDetails([
                            'pid' => $project->id,
                            'project_type' => $project::getProjectType(),
                            'domain' => $project->domain,
                            'ssl_cert_item_id' => $sslItem->id
                        ]);

                        break;

                    case ProjectInterface::PROJECT_TYPE_STORE:

                        $order = new Orders();
                        $order->cid = $project->customer_id;
                        $order->status = Orders::STATUS_PAID;
                        $order->hide = Orders::HIDDEN_OFF;
                        $order->processing = Orders::PROCESSING_NO;
                        $order->domain = $project->domain;
                        $order->item = Orders::ITEM_FREE_SSL;
                        $order->ip = '127.0.0.1';
                        $order->setDetails([
                            'pid' => $project->id,
                            'project_type' => $project::getProjectType(),
                            'domain' => $project->domain,
                            'ssl_cert_item_id' => $sslItem->id
                        ]);

                        break;

                    default:
                        throw new Exception('Undefined Project type!');
                        break;
                }

                if (!$order->save(false)) {
                    throw new CronException('Cannot create order!');
                }

                $this->stdout('Letsencrypt SSL-order has been created successfully! Order ID [ ' . $order->id . ' ]' . PHP_EOL);

            }
        }

        $this->stdout($this->cronTaskName() . ' finished', Console::FG_GREEN);
    }
}