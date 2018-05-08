<?php
namespace my\helpers;

use common\helpers\DbHelper;
use common\helpers\SuperTaskHelper;
use common\models\common\ProjectInterface;
use common\models\stores\StoreAdmins;
use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\components\domains\Ahnames;
use my\helpers\order\OrderDomainHelper;
use common\models\panels\AdditionalServices;
use common\models\panels\Domains;
use common\models\panels\ExpiredLog;
use common\models\panels\Project;
use common\models\panels\ProjectAdmin;
use common\models\panels\UserServices;
use Yii;
use my\components\dictionaries\SslCertAsGoGetSsl;
use my\components\ssl\Ssl;
use common\models\panels\Orders;
use common\models\panels\SslCert;
use common\models\panels\SslCertItem;
use common\models\panels\SslValidation;
use common\models\panels\ThirdPartyLog;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use my\helpers\order\OrderSslHelper;

/**
 * Class OrderHelper
 * @package my\helpers
 */
class OrderHelper {

    /**
     * Process ssl order
     * @param Orders $order
     * @return bool
     */
    public static function ssl(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $details = ArrayHelper::getValue($orderDetails, 'details');

        $projectType = ArrayHelper::getValue($orderDetails,'project_type', ProjectInterface::PROJECT_TYPE_PANEL);

        $sslItem = SslCertItem::findOne(ArrayHelper::getValue($orderDetails, 'item_id'));

        if (empty($sslItem)) {
            return false;
        }

        // Generate ssl csr code
        $csr = OrderSslHelper::generateCSR($order);

        if (empty($csr['success'])) {
            // Change order status to error
            $order->status = Orders::STATUS_ERROR;
            $order->save(false);
            return false;
        }

        // Add ssl order
        $orderSsl = OrderSslHelper::addSSLOrder($order, [
            'product_id' => $sslItem->product_id,
            'csr' => $csr['csr_code'],
        ]);

        if (empty($orderSsl['success'])) {
            // Change order status to error
            $order->status = Orders::STATUS_ERROR;
            $order->save(false);
            return false;
        }

        // Save ssl validation file name and content
        $validation = ArrayHelper::getValue($orderSsl, 'approver_method', ArrayHelper::getValue($orderSsl, 'validation'));
        $validation = ArrayHelper::getValue($validation, Ssl::DCV_METHOD_HTTP);

        $sslValidation = new SslValidation();
        $sslValidation->pid = ArrayHelper::getValue($orderDetails, 'pid');
        $sslValidation->file_name = ArrayHelper::getValue($validation, 'filename');
        $sslValidation->content = ArrayHelper::getValue($validation, 'content');

        if (!$sslValidation->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, $sslValidation->getErrors(), 'cron.ssl.ssl_validation');
        }

        // Create ssl cert data with csr and ssl order data details
        $sslCert = new SslCert();
        $sslCert->checked = SslCert::CHECKED_NO;
        $sslCert->cid = $order->cid;
        $sslCert->item_id = $sslItem->id;
        $sslCert->pid = ArrayHelper::getValue($orderDetails, 'pid');
        $sslCert->domain = $order->domain;
        $sslCert->project_type = $projectType;
        $sslCert->csr_code = ArrayHelper::getValue($csr, 'csr_code');
        $sslCert->csr_key = ArrayHelper::getValue($csr, 'csr_key');

        $sslCert->setOrderDetails($orderSsl);
        $sslCert->setCsrDetails($csr);

        if (!$sslCert->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, $sslCert->getErrors(), 'cron.ssl.ssl');
            // Change order status to error
            $order->status = Orders::STATUS_ERROR;
            $order->save(false);
            return false;
        }

        // Change order status to added
        $order->status = Orders::STATUS_ADDED;
        $order->item_id = $sslCert->id;
        $order->save(false);

        $sslCert->createdNotice();

        return true;
    }

    /**
     * Update ssl order status
     * @param SslCert $ssl
     * @return bool
     */
    public static function updateSslOrderStatus(SslCert $ssl)
    {
        $order = $ssl->getOrderDetails();
        $project = $ssl->project;

        $orderId = ArrayHelper::getValue($order, 'order_id');

        if (!$orderId || !$project) {
            return false;
        }

        $orderDetails = OrderSslHelper::getOrderStatus($ssl);

        if (empty($orderDetails['success'])) {
            return false;
        }

        Yii::info(Json::encode($orderDetails), 'ssl_order_status');

        $status = ArrayHelper::getValue($orderDetails, 'status');
        $status = SslCertAsGoGetSsl::getSslCertStatus($status);
        $crt = ArrayHelper::getValue($orderDetails, 'crt_code');
        $ca = ArrayHelper::getValue($orderDetails, 'ca_code');

        if (!$status) {
            return false;
        }

        if (SslCert::STATUS_ACTIVE == $status) {

            if (empty($crt) || empty($ca)) {
                return false;
            }

            // Save order status details
            $ssl->setOrderStatusDetails($orderDetails);

            $crtKey = $crt . "\n" . $ca;

            // $crt + $ca code
            if (!(OrderSslHelper::addDdos($ssl, [
                'site' => $project->getBaseDomain(),
                'crt' => $crtKey,
                'key' => $ssl->csr_key,
            ]))) {
                $status = SslCert::STATUS_DDOS_ERROR;
            }

            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, [
                'domain' => $project->getBaseDomain(),
                'crt_cert' => $crtKey,
                'key_cert' => $ssl->csr_key,
                'key' => Yii::$app->params['system.sslScriptKey']
            ], 'cron.ssl_status.send_ssl_config');

            if (!(OrderSslHelper::addConfig($ssl, [
                'domain' => $project->getBaseDomain(),
                'crt_cert' => $crtKey,
                'key_cert' => $ssl->csr_key,
            ]))) {
                $status = SslCert::STATUS_DDOS_ERROR;
            }

            if (!$ssl->changeStatus($status)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, $ssl->getErrors(), 'cron.ssl_status');
                return false;
            }

        } else {
            if (!$ssl->changeStatus($status)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, $ssl->getErrors(), 'cron.ssl_status');
                return false;
            }
        }

        return true;
    }

    /**
     * Create project
     * @param Orders $order
     * @param boolean $child
     * @return bool
     */
    public static function panel(Orders $order, $child = false)
    {
        $orderDetails = $order->getDetails();
        $projectDefaults = Yii::$app->params['projectDefaults'];
        $domain = ArrayHelper::getValue($orderDetails, 'clean_domain');

        $project = new Project();
        $project->attributes = $projectDefaults;

        $project->act = Project::STATUS_ACTIVE;
        $project->cid = $order->cid;
        $project->site = $domain;
        $project->name = DomainsHelper::idnToUtf8($domain);
        $project->currency = ArrayHelper::getValue($orderDetails, 'currency');
        $project->generateDbName();
        $project->generateExpired();

        if ($child) {
            $project->child_panel = 1;
            $project->provider_id = ArrayHelper::getValue($orderDetails, 'provider');
            $project->plan = Project::DEFAULT_CHILD_TARIFF;
            $project->tariff = Project::DEFAULT_CHILD_TARIFF;
        }

        if (!$project->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, $project->getErrors(), 'cron.order.panel');
            return false;
        }

        $ExpiredLogModel = new ExpiredLog();
        $ExpiredLogModel->attributes = [
            'pid' => $project->id,
            'expired_last' => 0,
            'expired' => $project->expired,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CREATE_EXPIRY
        ];
        $ExpiredLogModel->save(false);

        // Change order status to added
        $order->status = Orders::STATUS_ADDED;
        $order->item_id = $project->id;
        $order->save(false);
        $order->refresh();

        $projectAdmin = new ProjectAdmin();
        $projectAdmin->pid = $project->id;
        $projectAdmin->login = ArrayHelper::getValue($orderDetails, 'username');
        $projectAdmin->passwd = ArrayHelper::getValue($orderDetails, 'password');
        $projectAdmin->setRules(ProjectAdmin::$defaultRules);

        if (!$projectAdmin->save(false)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $projectAdmin->getErrors(), 'cron.order.admin');
        }

        // проверяем добавлена ли ранее запись additional_services.name = домен панели, если да то добавляем к имени _old
        // и меняем additional_services.status = 1 и additional_services.search = 1
        $additionalService = AdditionalServices::findOne([
            'name' => $domain
        ]);
        if ($additionalService) {
            $additionalService->name = $additionalService->name . '_old';
            $additionalService->search = 1;
            $additionalService->status = 1;
            $additionalService->save(false);
        }

        $additionalService = new AdditionalServices();
        $additionalService->name = $domain;
        $additionalService->type = AdditionalServices::TYPE_INTERNAL;
        $additionalService->auto_order = 1;
        $additionalService->string_name = 1;
        $additionalService->auto_services = 1;
        $additionalService->sc = 1;
        $additionalService->generateApiHelp($domain);
        $additionalService->status = AdditionalServices::STATUS_ACTIVE;

        if (!$additionalService->save(false)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $additionalService->getErrors(), 'cron.order.service');
        }

        if ($child) {
            $userService = new UserServices();
            $userService->pid = $project->id;
            $userService->aid = $project->provider_id;
            $userService->save(false);
        }

        // Create nginx config
        SuperTaskHelper::setTasksNginx($project, [
            'order_id' => $order->id
        ]);

        // Create panel db
        if (!DbHelper::existDatabase($project->db)) {
            DbHelper::createDatabase($project->db);
        }

        if (!DbHelper::existDatabase($project->db)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, '', 'cron.order.db');
        }

        // Deploy panel tables
        if (DbHelper::existDatabase($project->db)) {
            $sqlPanelPath = Yii::$app->params['panelSqlPath'];
            if (file_exists($sqlPanelPath)) {
                DbHelper::dumpSql($project->db, $sqlPanelPath);
            }
        }

        if (!$project->enableDomain()) {
            $order->status = Orders::STATUS_ERROR;
        }

        // Change status
        if (Orders::STATUS_ADDED != $order->status) {
            $order->save(false);

            return false;
        }

        $project->createdNotice();

        return true;
    }

    /**
     * Create domain
     * @param Orders $order
     * @return bool
     */
    public static function domain(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $domain = ArrayHelper::getValue($orderDetails, 'domain');
        $zoneId = ArrayHelper::getValue($orderDetails, 'zone');
        $details = ArrayHelper::getValue($orderDetails, 'details', []);
        $contactResult = ArrayHelper::getValue($orderDetails, 'domain_contact');
        $domainResult = ArrayHelper::getValue($orderDetails, 'domain_register');
        $domainInfoResult = ArrayHelper::getValue($orderDetails, 'domain_info');

        if (empty($contactResult)) {
            $contactResult = OrderDomainHelper::contactCreate($order);

            if (empty($contactResult['id'])) {
                if (!empty($contactResult['_error']) && false === strpos(strtolower($contactResult['_error']), 'wait')) {
                    $order->makeError();
                    return false;
                }

                $order->finish();
                return false;
            }

            $order->setItemDetails($contactResult, 'domain_contact');
            $order->save(false);
            $order->refresh();
        }

        if (empty($domainResult)) {
            $domainResult = OrderDomainHelper::domainRegister($order);

            if (!empty($domainResult[$domain][0])) {
                $domainResult = $domainResult[$domain][0];
            } elseif (empty($domainResult['id'])) {
                if (!empty($domainResult['_error']) && false !== strpos(strtolower($domainResult['_error']), 'wait')) {
                    // Возвращает массив
                    if (!empty($domainResult[0]['wait'])) {
                        $order->finish();
                        return false;
                    }
                }
                $order->makeError();
                return false;
            }

            $order->setItemDetails($domainResult, 'domain_register');
            $order->save(false);
            $order->refresh();
        }

        if (empty($domainInfoResult)) {

            $domainInfoResult = OrderDomainHelper::domainGetInfo($order);

            if (empty($domainInfoResult['id'])) {
                $order->makeError();
                return false;
            }

            $order->setItemDetails($domainInfoResult, 'domain_info');
            $order->save(false);
            $order->refresh();
        }

        $expiry = ArrayHelper::getValue($domainInfoResult, 'expires');
        $expiry = strtotime($expiry);

        $domainModel = new Domains();
        $domainModel->customer_id = $order->cid;
        $domainModel->zone_id = $zoneId;
        $domainModel->domain = $domain;
        $domainModel->contact_id = $contactResult['id'];
        $domainModel->status = Domains::STATUS_OK;
        $domainModel->password = ArrayHelper::getValue($domainResult, 'password');
        $domainModel->setItemDetails($contactResult, 'domain_contact');
        $domainModel->setItemDetails($domainResult, 'domain_register');
        $domainModel->setItemDetails($domainInfoResult, 'domain_info');
        $domainModel->expiry = $expiry;
        $domainModel->privacy_protection = (int)!empty($details['domain_protection']);
        $domainModel->transfer_protection = 1;

        if (!$domainModel->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_DOMAIN, $order->id, $domainModel->getErrors(), 'cron.order.domain');
            return false;
        }

        $orderStatus = Orders::STATUS_ADDED;

        if (!empty($details['domain_protection'])) {

            $assignResult = OrderDomainHelper::domainEnableWhoisProtect($order);

            if (!empty($assignResult['id'])) {
                $domainModel->refresh();
                $domainModel->setItemDetails($assignResult, 'domain_protection');
                $domainModel->save(false);
            } else {
                $orderStatus = Orders::STATUS_ERROR;
            }
        }

        $setNss = OrderDomainHelper::domainSetNSs($order);

        if (!empty($setNss['id'])) {
            $domainModel->refresh();
            $domainModel->setItemDetails($setNss, 'domain_nss');
            $domainModel->save(false);
        } else {
            $orderStatus = Orders::STATUS_ERROR;
        }

        $enableLock = OrderDomainHelper::domainEnableLock($order);

        if (!empty($enableLock['id'])) {
            $domainModel->refresh();
            $domainModel->setItemDetails($enableLock, 'domain_lock');
            $domainModel->save(false);
        } else {
            $orderStatus = Orders::STATUS_ERROR;
        }

        $order->item_id = $domainModel->id;
        $order->status = $orderStatus;
        $order->save(false);

        if (Orders::STATUS_ADDED != $orderStatus) {
            return false;
        }

        $domainModel->createdNotice();
    }

    /**
     * Create store
     * @param Orders $order
     * @return bool
     */
    public static function store(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $isTrial = (bool)ArrayHelper::getValue($orderDetails, 'trial', false);

        $projectDefaults = Yii::$app->params['store.defaults'];

        $store = new Stores();
        $store->setAttributes($projectDefaults);

        $store->customer_id = $order->cid;
        $store->admin_email = ArrayHelper::getValue($orderDetails, 'admin_email');
        $store->currency = ArrayHelper::getValue($orderDetails,'currency');
        $store->domain = DomainsHelper::idnToUtf8($order->domain);
        $store->subdomain = 0;
        $store->name = ArrayHelper::getValue($orderDetails,'name');
        $store->status = Stores::STATUS_ACTIVE;
        $store->trial = $isTrial;
        $store->generateExpired($isTrial);

        if (!$store->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $order->id, $store->getErrors(), 'cron.order.store');
            return false;
        }

        $store->generateDbName();

        if (!$store->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $order->id, $store->getErrors(), 'cron.order.store');
            return false;
        }

        $expiredLog = new ExpiredLog();
        $expiredLog->setAttributes([
            'pid' => $store->id,
            'expired_last' => 0,
            'expired' => $store->expired,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CREATE_STORE_EXPIRY
        ]);
        $expiredLog->save(false);

        $order->status = Orders::STATUS_ADDED;
        $order->item_id = $store->id;
        $order->save(false);
        $order->refresh();

        $storeAdmin = new StoreAdmins();
        $storeAdmin->store_id = $store->id;
        $storeAdmin->username = ArrayHelper::getValue($orderDetails,'username');
        $storeAdmin->password = ArrayHelper::getValue($orderDetails,'password');
        $storeAdmin->status = StoreAdmins::STATUS_ACTIVE;
        $storeAdmin->setRules(StoreAdmins::$defaultRules);

        if (!$storeAdmin->save(false)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $storeAdmin->getErrors(), 'cron.order.store_admin');
        }

        $storeDomain = new StoreDomains();
        $storeDomain->store_id = $store->id;
        $storeDomain->domain = $store->domain;
        $storeDomain->type = StoreDomains::DOMAIN_TYPE_SOMMERCE;
        $storeDomain->ssl = StoreDomains::SSL_OFF;

        if (!$storeDomain->save(false)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $storeAdmin->getErrors(), 'cron.order.store_domain');
        }

        // Create Store db
        if (!DbHelper::existDatabase($store->db_name)) {
            DbHelper::createDatabase($store->db_name);
        }

        if (!DbHelper::existDatabase($store->db_name)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, '', 'cron.order.store_db');
        }

        $storeSqlPath = Yii::$app->params['storeSqlPath'];

        // Make Sql dump from store template db
        if (!DbHelper::makeSqlDump(Yii::$app->params['storeDefaultDatabase'], $storeSqlPath)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $storeSqlPath, 'cron.order.make_sql_dump');
        }

        // Deploy Sql dump to store db
        if (!DbHelper::dumpSql($store->db_name, $storeSqlPath)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $storeSqlPath, 'cron.order.deploy_sql_dump');
        }

        // Change status
        if (Orders::STATUS_ADDED != $order->status) {
            $order->save(false);

            return false;
        }

        return true;
    }
}