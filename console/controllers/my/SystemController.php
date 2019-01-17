<?php
namespace console\controllers\my;

use common\components\letsencrypt\AcmeInstaller;
use common\helpers\PaymentHelper;
use common\models\panels\Customers;
use common\models\panels\Domains;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Languages;
use common\models\panels\Orders;
use common\models\panels\PanelDomains;
use common\models\panels\Params;
use common\models\panels\PaymentGateway;
use common\models\panels\Payments;
use common\models\panels\Project;
use common\models\panels\ProjectAdmin;
use common\models\panels\SslCert;
use common\models\panels\SuperAdmin;
use common\models\panels\Tickets;
use console\components\payments\PaymentsFee;
use Faker\Factory;
use common\components\dns\Dns;
use my\components\ActiveForm;
use common\helpers\DnsHelper;
use my\helpers\DomainsHelper;
use common\helpers\SuperTaskHelper;
use Yii;
use yii\console\ExitCode;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use common\models\panels\AdditionalServices;
use common\helpers\CurrencyHelper;
use Exception;
use my\helpers\order\OrderSslHelper;

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

    /**
     * Update the category column which it is empty
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdateParams()
    {
        $methods = Params::find()
            ->where('category IS NULL')
            ->all();


        foreach ($methods as $method) {
            $this->stderr("Updating {$method->code} \n", Console::FG_GREEN);

            $updateData = explode('.', $method->code);
            $method->category = $updateData[0];
            $method->code = $updateData[1];
            $method->update(false);

            $this->stderr("Successful update {$method->code} \n", Console::FG_GREEN);
        }
    }

    /**
     * Transfer data from payment_gateway to params
     */
    public function actionTransferToParams()
    {
        $payments = PaymentGateway::find()->where(['pid' => -1])->all();
        $category = 'payment';

        foreach ($payments as $payment) {
            $this->stderr("Transfer {$payment->name} \n", Console::FG_GREEN);

            $params = new Params();

            $code = strtolower(str_replace(' ', '_', $payment->name));
            $options = ['credentials' => $payment->getOptionsData()];
            $options = array_merge((array)$options, $payment->getAttributes([
                'name',
                'minimal',
                'maximal',
                'visibility',
                'fee',
                'type',
                'dev_options',
            ]));
            $params->code = $code;
            $params->category = $category;
            $params->setOptions($options);
            $params->position = $payment->position;
            if (!$params->save()) {
                $this->stderr(ActiveForm::firstError($params) . "\n", Console::FG_RED);
            } else {
                $this->stderr("Successful \n", Console::FG_GREEN);
            }
        }
    }

    /**
     * Change additional_services.currency
     */
    public function actionSslFix()
    {
        $sslQuery = SslCert::find()->andWhere([
            'status' => SslCert::STATUS_ACTIVE
        ]);

        foreach ($sslQuery->batch() as $sslList) {
            foreach ($sslList as $ssl) {
                $this->stderr($ssl->domain . "\n", Console::FG_GREEN);

                $json = json_decode($ssl->details);
                if ($json !== false) {
                    try {
                        if (!empty($json->order_status->crt_code) and !empty($json->order_status->ca_code) and !empty($json->csr->csr_key)) {

                            $crt = $json->order_status->crt_code;
                            $ca = $json->order_status->ca_code;

                            $crtKey = $crt . "\n" . $ca;
                            $csrKey = $json->csr->csr_key;

                            if (!OrderSslHelper::addConfig($ssl, [
                                'domain' => $ssl->domain,
                                'crt_cert' => $crtKey,
                                'key_cert' => $csrKey,
                            ])) {
                                $this->stderr('Can not add config for ' . $ssl->domain . "\n", Console::FG_RED);
                            } else {
                                $this->stderr('Config for has been added for ' . $ssl->domain . "\n", Console::FG_GREEN);
                            }
                        } else {
                            $this->stderr('Can not add config for ' . $ssl->domain . ". Details: empty required json data \n", Console::FG_RED);
                        }


                    } catch (Exception $e) {
                        $this->stderr('Can not add config for ' . $ssl->domain . ". Details: " . $e->getMessage() . " \n", Console::FG_RED);
                    }
                } else {
                    $this->stderr('Can not add config for ' . $ssl->domain . ". Details: invalid json details \n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * Update timezone
     */
    public function actionUpdateTimezones()
    {
        $timezoneList = Yii::$app->params['timezones'];

        $customers = (new Query())
            ->select(['id', 'timezone'])
            ->from(DB_PANELS . '.customers')
            ->all();

        $panels = (new Query())
            ->select(['id', 'utc'])
            ->from(DB_PANELS . '.project')
            ->all();

        $stores = (new Query())
            ->select(['id', 'timezone'])
            ->from(DB_STORES . '.stores')
            ->all();

        $models = [
            'customers' => $customers,
            'panels' => $panels,
            'stores' => $stores,
        ];

        foreach ($models as $key => $value) {
            $column = '';
            $class = '';

            switch ($key) {
                case 'customers' :
                    $column = 'timezone';
                    $class = 'common\models\panels\Customers';
                    break;
                case 'panels' :
                    $column = 'utc';
                    $class = 'common\models\panels\Project';
                    break;
                case 'stores' :
                    $column = 'timezone';
                    $class = 'common\models\stores\Stores';
                    break;
            }

            $tabel = substr($key, 0, -1);

            foreach ($value as $model) {
                if (!isset($timezoneList[$model[$column]])) {
                    $newTimezone = round($model[$column], -2);
                    if (isset($timezoneList[$newTimezone])) {
                        echo "Updating the $tabel ID = {$model['id']} \n";
                        $updatedColumns = $class::updateAll([$column => $newTimezone], ['id' => $model['id']]);
                        if ($updatedColumns == 0) {
                            echo "Not updated \n";
                        } else {
                            echo "Successful update \n";
                        }
                    }
                }
            }
        }
    }

    /**
     * Set code to payment_method column of payments table
     */
    public function actionSetPaymentMethods()
    {
        $paymentQuery = Payments::find()
            ->where('payment_method IS NULL');

        foreach ($paymentQuery->batch() as $payments) {
            foreach ($payments as $payment) {
                /**
                 * @var Payments $payment
                 */
                $payment->payment_method = PaymentHelper::getCodeByType($payment->type);
                if ($payment->save(false)) {
                    $this->stderr("Successful update the payment #{$payment->id} \n", Console::FG_GREEN);
                } else {
                    $this->stderr("Payment #{$payment->id} : " . ActiveForm::firstError($payment) . "\n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * Installed ACME.sh library to the MY project
     * @return int
     */
    public function actionAcme()
    {
        $this->stdout('Letsencrypt ACME.sh library management script'. PHP_EOL, Console::FG_GREEN);

        $installer = new AcmeInstaller();
        $installer->console = $this;

        try{
            $installer->run();
        } catch (\Exception $exception) {
             $this->stderr(PHP_EOL . $exception->getMessage() . PHP_EOL . PHP_EOL, Console::FG_RED);
        }

        if ($this->confirm('Exit from ACME?')) {
            return ExitCode::OK;
        }

        return $this->run($this->route);
    }

    public function actionAdminFullAccess()
    {
        $staffsBatch = ProjectAdmin::find();

        /**
         * @var $staff ProjectAdmin
         */
        foreach ($staffsBatch->batch() as $staffs) {
            foreach ($staffs as $staff) {
                if ($staff->isFullAccess()) {
                    $staff->setRules(ProjectAdmin::$defaultRules);
                    $staff->save(false);
                    $this->stderr('Migrated rules staff ' . $staff->id . "\n", Console::FG_GREEN);
                }
            }
        }
    }

    /**
     * Update default affiliate parameters
     */
    public function actionUpdateDefaultAffiliates()
    {
        Project::updateAll([
            'affiliate_minimum_payout' => 10,
            'affiliate_commission_rate' => 5,
            'affiliate_approve_payouts' => 0,
        ], 'affiliate_minimum_payout IS NULL');
    }

    /**
     * Updates the old IP address with the new one. Both values are passed as parameters (oldIp, newIp)
     * @param string $oldIp Old IP address to be changed
     * @param string $newIp new ip to be saved
     * @return string
     */
    public function actionChangeCloudnsIp($oldIp, $newIp): string
    {
        $pagesCount = 42;
        $domainRawList = '';
        $domainList = [];
        $count = 777;

        $cloudId = Yii::$app->params['dnsId'];
        $cloudPass = Yii::$app->params['dnsPassword'];

        $cloudCountLink = "https://api.cloudns.net/dns/get-pages-count.json?auth-id={$cloudId}&auth-password={$cloudPass}&page=1&rows-per-page=100";

        // TODO вынести в функцию
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $cloudCountLink);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        $domainCount = curl_exec($ch);

        $this->stdout("Got a list of {$pagesCount} pages\n");

//        for ($i = 1; $i <= $domainCount; $i++) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, "https://api.cloudns.net/dns/list-zones.json?auth-id={$cloudId}&auth-password={$cloudPass}&page={$i}&rows-per-page=100");
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            $domainRawList += curl_exec($ch);
//        }

        $domainRawList = '[{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.me","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.xyz","type":"master","zone":"domain","status":"1"},{"name":"domain.net","type":"master","zone":"domain","status":"1"},{"name":"domain.net","type":"master","zone":"domain","status":"1"},{"name":"domain.pro","type":"master","zone":"domain","status":"1"},{"name":"domain.name","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.net","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"},{"name":"domain.com","type":"master","zone":"domain","status":"1"}]';

        $domainRawList = json_decode($domainRawList[], true);

        Yii::debug($domainRawList);

        foreach ($domainRawList as $item) {
            $domains = $item['name'];
        }

        $domainCount = count($domains);

        $this->stdout("Got a list of {$domainCount} domains\n");

        foreach ($domains as $item) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, "https://api.cloudns.net/dns/records.json?auth-id={$cloudId}&auth-password={$cloudPass}&domain-name=$item");
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            $domainRawList += curl_exec($ch);
            Yii::debug($item);
            //TODO часть операций в одном цикле
        }

        $rawResponse = '{"31749306":{"id":"31749306","type":"A","host":"","record":"51.255.71.105","dynamicurl_status":0,"failover":"0","ttl":"60","status":1},"33129686":{"id":"33129686","type":"NS","host":"","record":"ns1.perfectdns.com","failover":"0","ttl":"300","status":1},"33129687":{"id":"33129687","type":"NS","host":"","record":"ns2.perfectdns.com","failover":"0","ttl":"300","status":1},"33129688":{"id":"33129688","type":"NS","host":"","record":"ns3.perfectdns.com","failover":"0","ttl":"300","status":1},"31749312":{"id":"31749312","type":"CNAME","host":"www","record":"getyourpanel.com","failover":"0","ttl":"300","status":1}}';
        $ip = json_decode($rawResponse, true);
        $ip = array_column($ip, 'A');

        if ($ip != $oldIp) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, "https://api.cloudns.net/dns/mod-record.json?sub-auth-id={$cloudId}&auth-password={$cloudPass}&domain-name={$item}&record-id={0000000}&host=&record={$newIp}&ttl=60");
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            $domainRawList += curl_exec($ch);
        }

        $this->stdout("We are looking for the old IP ({$oldIp}) on the list of domains\n");


        $this->stdout("We receive a list of DNS records for the domain\n");


        $this->stdout("We change old ip on new ({$newIp})\n");

        return $this->stdout("\nSuccessfully changed {$count} ip addresses\n", Console::FG_GREEN);
    }
}