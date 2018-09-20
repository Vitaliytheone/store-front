<?php
namespace console\controllers\my;

use common\models\panels\Customers;
use common\models\panels\Domains;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Languages;
use common\models\panels\Orders;
use common\models\panels\PanelDomains;
use common\models\panels\Project;
use common\models\panels\ProjectAdmin;
use common\models\panels\SslCert;
use common\models\panels\Tickets;
use console\components\payments\PaymentsFee;
use Faker\Factory;
use common\components\dns\Dns;
use my\helpers\DnsHelper;
use my\helpers\DomainsHelper;
use common\helpers\SuperTaskHelper;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use common\models\panels\AdditionalServices;
use common\helpers\CurrencyHelper;

/**
 * Class SystemController
 * @package console\controllers\my
 */
class SystemController extends CustomController
{
    public $start;

    public function options($actionID)
    {
        return ['start'];
    }

    /**
     * Intersect project domains and dns data
     */
    public function actionIntersectProjectDns()
    {
        $stopDomains = [
            'getyourpanel.com',
            'myperfectpanel.com',
            'mysommerce.com',
            'perfectpanel.com',
            'sommerce.com',
            'sommerce.net'
        ];
        $stopSubdomains = [
            'test',
            'www'
        ];
        $dnsList = ArrayHelper::index(Dns::listZones([], $results), 'name');

        foreach (Project::find()->andWhere([
            'act' => [
                Project::STATUS_ACTIVE,
                Project::STATUS_FROZEN
            ]
        ])->andWhere(['not in', 'site', $stopDomains])->batch(1000) as $projects) {
            $projects = ArrayHelper::index($projects, 'site');

            foreach ($dnsList as $domain => $dnsRow) {
                $domain = DomainsHelper::idnToUtf8($domain);
                if (in_array($domain, $stopDomains)) {
                    continue;
                }

                if (empty($projects[$domain])) {
                    // Do something
                    var_dump($domain);
                }
            }
        }
        unset($projects, $dnsList);

        $panelRecords = ArrayHelper::index(Dns::listRecords(Yii::$app->params['panelDomain'], '', [
            'type' => 'CNAME'
        ], $results), 'host');

        foreach (Project::find()
             ->andWhere(['not in', 'site', $stopDomains])
             ->batch(1000) as $projects) {
            $projects = ArrayHelper::index($projects, 'site');
            $projects = array_reduce($projects, function ($result, $item) {
                $result[str_replace('.', '-', $item->site)] = $item;
                return $result;
            }, []);

            foreach ($panelRecords as $domain => $dnsRecord) {
                $domain = DomainsHelper::idnToUtf8($domain);
                if (in_array($domain, $stopSubdomains)) {
                    continue;
                }

                if (empty($projects[$domain])) {
                    // Do something
                    var_dump($domain);
                }
            }
        }
    }

    /**
     * Re count tickets
     */
    public function actionRecountTickets()
    {
        $ticketsQuery = Tickets::find();

        $ticketsQuery->andWhere('id > :start', [
            ':start' => $this->start
        ]);

        $counter = $this->start;
        foreach ($ticketsQuery->all() as $ticket) {
            $counter++;

            if ($ticket->id == $counter) {
                continue;
            }

            $sql = "
                SET foreign_key_checks = 0;
                UPDATE `ticket_messages` SET `tid` = '{$counter}' WHERE `tid` = '{$ticket->id}';
                UPDATE `tickets` SET `id` = '{$counter}' WHERE `id` = '{$ticket->id}';
                SET foreign_key_checks = 1;
            ";

            Yii::$app->db->createCommand($sql)->execute();
        }

        Yii::$app->db->createCommand("ALTER TABLE `tickets` auto_increment = {$counter};")->execute();
    }

    /**
     * Re count orders
     */
    public function actionRecountOrders()
    {
        $ordersQuery = Orders::find();

        $ordersQuery->andWhere('id > :start', [
            ':start' => $this->start
        ]);

        $counter = $this->start;
        foreach ($ordersQuery->all() as $order) {
            $counter++;

            if ($order->id == $counter) {
                continue;
            }

            $sql = "SET foreign_key_checks = 0;";

            $invoiceDetails = InvoiceDetails::find()->andWhere([
                'item_id' => $order->id,
                'item' => [1,3,5]
            ])->one();

            if ($invoiceDetails) {
                $sql .= "UPDATE `payments` SET `pid` = '{$counter}' WHERE `pid` = '{$order->id}' AND iid = {$invoiceDetails->invoice_id};";
            }

            $sql .= "
                UPDATE `third_party_log` SET `item_id` = '{$counter}' WHERE `item_id` = '{$order->id}' AND item IN (3,7);
                UPDATE `invoice_details` SET `item_id` = '{$counter}' WHERE `item_id` = '{$order->id}' AND item IN (1,3,5);
                UPDATE `orders` SET `id` = '{$counter}' WHERE `id` = '{$order->id}';
                SET foreign_key_checks = 1;
            ";

            Yii::$app->db->createCommand($sql)->execute();
        }

        Yii::$app->db->createCommand("ALTER TABLE `orders` auto_increment = {$counter};")->execute();
    }

    /**
     * Re count invoices
     */
    public function actionRecountInvoices()
    {
        $invoicesQuery = Invoices::find();

        $invoicesQuery->andWhere('id > :start', [
            ':start' => $this->start
        ]);

        $counter = $this->start;
        foreach ($invoicesQuery->all() as $invoice) {
            $counter++;

            if ($invoice->id == $counter) {
                continue;
            }

            $sql = "
                SET foreign_key_checks = 0;
                UPDATE `invoice_details` SET `invoice_id` = '{$counter}' WHERE `invoice_id` = '{$invoice->id}';
                UPDATE `payments` SET `iid` = '{$counter}' WHERE `iid` = '{$invoice->id}';
                UPDATE `invoices` SET `id` = '{$counter}' WHERE `id` = '{$invoice->id}';
                SET foreign_key_checks = 1;
            ";

            Yii::$app->db->createCommand($sql)->execute();
        }

        Yii::$app->db->createCommand("ALTER TABLE `invoices` auto_increment = {$counter};")->execute();
    }

    /**
     * Intersect terminated domains
     */
    public function actionIntersectTerminatedDomains()
    {
        foreach (Project::find()
         ->leftJoin('panel_domains', 'panel_domains.panel_id = project.id AND panel_domains.type = ' . PanelDomains::TYPE_SUBDOMAIN)
         ->andWhere('panel_domains.id IS NULL')
         ->andWhere([
             'project.act' => Project::STATUS_TERMINATED
         ])
         ->batch(1000) as $projects) {
            foreach ($projects as $project) {
                $subPrefix = str_replace('.', '-', $project->site);
                $panelDomainName = Yii::$app->params['panelDomain'];
                $subDomain = $subPrefix . '.' . $panelDomainName;

                $panelDomain = new PanelDomains();
                $panelDomain->type = PanelDomains::TYPE_SUBDOMAIN;
                $panelDomain->panel_id = $project->id;
                $panelDomain->domain = $subDomain;

                $panelDomain->save();
            }
        }

        $panelRecords = ArrayHelper::index(Dns::listRecords(Yii::$app->params['panelDomain'], '', [
            'type' => 'CNAME'
        ], $results), 'host');

        foreach (PanelDomains::find()
                ->andWhere([
                    'panel_domains.type' => PanelDomains::TYPE_SUBDOMAIN,
                    'project.act' => Project::STATUS_TERMINATED,
                ])
                ->innerJoinWith(['panel'])
                ->batch(1000) as $domains) {


            foreach ($domains as $item) {
                $domain = substr($item->domain, 0, strpos($item->domain, "."));

                if (empty($panelRecords[$domain])) {
                    DnsHelper::addSubDns($item->panel);
                }
            }
        }
    }

    /**
     * Migrate message from ticket to
     */
    public function actionMigrateTicketMessages()
    {
        Yii::$app->db->createCommand('
            INSERT INTO ticket_messages (`message`, `cid`, `tid`, `date`)
            SELECT `message`, `cid`, `id`, `date` FROM tickets
            WHERE tickets.admin_id = 0
        ')->execute();

        Yii::$app->db->createCommand('
            INSERT INTO ticket_messages (`message`, `tid`, `date`, `uid`)
            SELECT `message`, `id`, `date`, (' . SuperAdmin::DEFAULT_ADMIN . ') FROM tickets
            WHERE tickets.admin_id > 0
        ')->execute();
    }

    /**
     * Update existed staffs passwords
     */
    public function actionHashStaffPasswords()
    {
        /**
         * @var ProjectAdmin $staff
         */
        foreach (ProjectAdmin::find()->all() as $staff) {
            $staff->setPassword($staff->passwd);
            $staff->save(false);
        }
    }

    /**
     * Add item id to orders
     */
    public function actionAddItemIdToOrders()
    {
        /**
         * @var $order Orders
         */
        foreach (Orders::find()
            ->andWhere('item_id = 0')
            ->all() as $order) {

            $itemModel = null;

            if (Orders::ITEM_BUY_PANEL == $order->item) {
                $itemModel = Project::findOne([
                    'site' => $order->domain,
                    'cid' => $order->cid
                ]);
            } else if (Orders::ITEM_BUY_DOMAIN == $order->item) {
                $itemModel = Domains::findOne([
                    'domain' => $order->domain,
                    'customer_id' => $order->cid
                ]);
            } else if (Orders::ITEM_BUY_SSL == $order->item) {
                $itemModel = SslCert::findOne([
                    'domain' => $order->domain,
                    'cid' => $order->cid
                ]);
            }

            if ($itemModel) {
                $order->item_id = $itemModel->id;
                $order->save(false);
            }
        }
    }

    /**
     * Generate nginx configs for active and frozen panels
     */
    public function actionGeneratePanelsNginxConfigs()
    {
        $projects = Project::find()->andWhere([
            'act' => [
                Project::STATUS_ACTIVE,
                Project::STATUS_FROZEN
            ]
        ])->all();

        foreach ($projects as $project) {
            /**
             * @var Project $project
             */
            
            SuperTaskHelper::setTasksNginx($project);
        }
    }

    public function actionGenerateAdminLog()
    {
        $project = Project::findOne(14);
        $userIds = ArrayHelper::getColumn((new Query())->select([
            'id'
        ])->from('project_admin')->all(), 'id');

        $events = Yii::$app->params['activityTypes'];

        $faker = Factory::create();
        for ($i = 0; $i < 2000000; $i++) {
            $ip = $faker->ipv4;
            $uid = $userIds[array_rand($userIds)];
            $type = array_rand($events);
            $details = $faker->text;
            $date = rand(1504224000, time());

            Yii::$app->db->createCommand("INSERT INTO " . $project->db . ".activity_log (ip, admin_id, `event`, details, `created_at`, super_user) VALUES ('{$ip}', {$uid}, {$type}, '{$details}', {$date}, 0)")->execute();
        }
    }

    public function actionGenerateReferrals()
    {
        $projectsBatch = Project::find()
            ->andWhere([
                'act' => [
                    Project::STATUS_ACTIVE,
                    Project::STATUS_FROZEN
                ]
            ])
            ->joinWith(['customer']);

        $customers = [];

        /**
         * @var $project Project
         */
        foreach ($projectsBatch->batch() as $projects) {
            foreach ($projects as $project) {
                if (isset($customers[$project->cid])) {
                    continue;
                }

                $customer = $project->customer;
                $customers[$project->cid] = $customer;

                $customer->paid = 0;
                $customer->generateReferralLink();
                $customer->activateReferral();
                $customer->save(false);

                $this->stderr('Generated for ' . $customer->id . "\n", Console::FG_GREEN);
            }
        }

        $customersBatch = Customers::find()
            ->andWhere('referral_link IS NULL');

        /**
         * @var $customer Customers
         */
        foreach ($customersBatch->batch() as $customers) {
            foreach ($customers as $customer) {
                $customer->generateReferralLink();
                $customer->save(false);
                $this->stderr('Generated for ' . $customer->id . "\n", Console::FG_GREEN);
            }
        }
    }

    public function actionActivateChildPanels()
    {
        $projectsBatch = Project::find()
            ->andWhere([
                'act' => [
                    Project::STATUS_ACTIVE,
                    Project::STATUS_FROZEN
                ]
            ])
            ->joinWith(['customer']);

        $customers = [];

        /**
         * @var $project Project
         */
        foreach ($projectsBatch->batch() as $projects) {
            foreach ($projects as $project) {
                if (isset($customers[$project->cid])) {
                    continue;
                }

                $customer = $project->customer;
                $customers[$project->cid] = $customer;

                $customer->activateChildPanels();

                $this->stderr('Activate child panel for ' . $customer->id . "\n", Console::FG_GREEN);
            }
        }
    }

    public function actionMigrateStaffRules()
    {
        $staffsBatch = ProjectAdmin::find();

        /**
         * @var $staff ProjectAdmin
         */
        foreach ($staffsBatch->batch() as $staffs) {
            foreach ($staffs as $staff) {

                $staff->setRules([
                    'users' => (int)!$staff->rules_users,
                    'orders' => (int)!$staff->rules_orders,
                    'subscription' => (int)!$staff->rules_subscriptions,
                    'tasks' => (int)!$staff->rules_tasks,
                    'dripfeed' => (int)!$staff->rules_dripfeed,
                    'services' => (int)!$staff->rules_services,
                    'payments' => (int)!$staff->rules_payments,
                    'tickets' => (int)!$staff->rules_tickets,
                    'reports' => (int)!$staff->rules_stats,
                    'providers' => (int)$staff->rules_providers,
                    'settings_general' => (int)!$staff->rules_settings,
                    'settings_providers' => (int)!$staff->rules_settings,
                    'settings_payments' => (int)!$staff->rules_settings,
                    'settings_bonuses' => (int)!$staff->rules_settings,
                    'settings_pages' => (int)!$staff->rules_settings,
                    'settings_menu' => (int)!$staff->rules_settings,
                    'settings_preferences' => (int)!$staff->rules_settings,
                    'settings_themes' => (int)!$staff->rules_themes,
                    'settings_languages' => (int)!$staff->rules_settings,
                ]);
                $staff->save(false);

                $this->stderr('Migrated rules staff ' . $staff->id . "\n", Console::FG_GREEN);
            }
        }
    }

    public function actionTestMessage()
    {
        echo Yii::t('app', 'ssl.created.ticket_subject');
    }

    public function actionPaymentsFee()
    {
        Yii::$container->get(PaymentsFee::class, [
            null, // days
            '2018-01-01'
        ])->run();
    }

    /**
     * Create default panel languages for active panels if its empty
     */
    public function actionCreatePanelLanguage()
    {
        $panels = Project::find()
            ->where(['act' => Project::STATUS_ACTIVE])
            ->all();

        foreach ($panels as $panel) {

            /** @var Project $panel */

            if (Languages::findOne(['panel_id' => $panel->id])) {
                continue;
            }

            $language = new Languages();
            $language->panel_id = $panel->id;
            $language->language_code = 'en';
            $language->name = Yii::$app->params['languages']['en'];
            $language->position = 1;
            $language->visibility = Languages::VISIBILITY_ON;
            $language->edited = Languages::EDITED_OFF;
            $language->save(false);

            $this->stderr('Created language for panel  ' . '[' . $panel->id . ' ' . $panel->name . ']' . "\n", Console::FG_GREEN);
        }
    }

    /**
     * Change additional_services.currency
     */
    public function actionChangeCurrency()
    {
        $additionalServices = AdditionalServices::find()->where(['type' => 1])->all();

        foreach ($additionalServices as $key => $service) {
            $panel = (new Query())
                ->select('currency')
                ->from(DB_PANELS.'.project')
                ->where(['site' => $service->name])
                ->one();

            if (empty($panel)) {
                echo "Panel $service->name is not exist \n";
            } else {
                $service->currency = CurrencyHelper::getCurrencyCodeById($panel['currency']);
                $service->save();
                echo "Changed currency at $service->name panel \n";
            }
        }
    }

    public function actionUpdateTimezones()
    {
        $customers = (new Query())
            ->select(['id', 'timezone'])
            ->from(DB_PANELS . '.customers')
            ->all();

        $timezoneList = Yii::$app->params['timezones'];
        foreach ($customers as $customer) {
            if (!isset($timezoneList[$customer['timezone']])) {
                $newTimezone = round($customer['timezone'], -2);
                if (isset($timezoneList[$newTimezone])) {
                    Customers::updateAll(['timezone' => $newTimezone], ['id' => $customer['id']]);
                }
            }
        }
    }
}