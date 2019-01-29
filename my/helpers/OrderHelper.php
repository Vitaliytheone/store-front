<?php

namespace my\helpers;

use common\components\domains\BaseDomain;
use common\components\letsencrypt\Letsencrypt;
use common\components\models\SslCertLetsencrypt;
use common\helpers\CurrencyHelper;
use common\helpers\DbHelper;
use common\helpers\SuperTaskHelper;
use common\models\common\ProjectInterface;
use common\models\gateways\Admins;
use common\models\gateways\Sites;
use common\models\panels\Languages;
use common\models\panels\PanelPaymentMethods;
use common\models\panels\PaymentMethods;
use common\models\panels\PaymentMethodsCurrency;
use common\models\panels\SuperAdmin;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use common\models\stores\StoreAdmins;
use common\models\stores\Stores;
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
use yii\base\Exception;
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
        $sslOrderData = [
            'product_id' => $sslItem->product_id,
            'csr' => $csr['csr_code'],
            'dcv_method' => $sslItem->getDcvMethod(),
        ];

        if ($sslItem->getDcvMethod() === Ssl::DCV_METHOD_EMAIL) {
            $sslOrderData['approver_email'] = SslCert::approverEmailByDomain($order->getDomain());
        }

        $orderSsl = OrderSslHelper::addSSLOrder($order, $sslOrderData);

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
                'site' => $project->domain,
                'crt' => $crtKey,
                'key' => $ssl->csr_key,
            ]))) {
                $status = SslCert::STATUS_ERROR;
            }

            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_SSL, $ssl->id, [
                'domain' => $project->domain,
                'crt_cert' => $crtKey,
                'key_cert' => $ssl->csr_key,
                'key' => Yii::$app->params['system.sslScriptKey']
            ], 'cron.ssl_status.send_ssl_config');

            if (!(OrderSslHelper::addConfig($ssl, [
                'domain' => $project->domain,
                'crt_cert' => $crtKey,
                'key_cert' => $ssl->csr_key,
            ]))) {
                $status = SslCert::STATUS_ERROR;
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
     * Process prolongation SSL
     * @param Orders $order
     * @return bool
     * @throws Exception
     */
    public static function prolongationSsl(Orders $order)
    {
        $sslCert = SslCert::findOne([
            'id' => $order->item_id,
        ]);

        if (empty($sslCert)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, 'SslCert not found', 'cron.ssl.prolong');

            throw new Exception("SslCert not found [$order->item_id]");
        }

        // Save old SslCert data before order renew ssl
        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, $sslCert->attributes, 'cron.ssl.prolong.old_data');

        $orderRenewSsl = OrderSslHelper::addSslRenewOrder($order, $sslCert);

        if (empty($orderRenewSsl['success'])) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, ['error' => 'Prolong SSL Api response not success!', 'data' => $orderRenewSsl], 'cron.ssl.prolong.api');

            return false;
        }

        // Update SslCert needed data
        $sslCert->checked = SslCert::CHECKED_NO;
        $sslCert->setOrderDetails($orderRenewSsl);

        if (!$sslCert->save(false)) {
            $sslCert->status = SslCert::STATUS_ERROR;
            $sslCert->save(false);

            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, ['error' => 'Error on SslCert update', 'data' => $sslCert->getErrors()], 'cron.ssl.prolong.save');

            throw new Exception("SslCert does not updated! [$order->item_id]");
        }

        // Save ssl validation file name and content
        $validation = ArrayHelper::getValue($orderRenewSsl, 'approver_method', ArrayHelper::getValue($orderRenewSsl, 'validation'));
        $validation = ArrayHelper::getValue($validation, Ssl::DCV_METHOD_HTTPS, ArrayHelper::getValue($validation, Ssl::DCV_METHOD_HTTP));

        $validFilename = ArrayHelper::getValue($validation, 'filename');
        $validContent = ArrayHelper::getValue($validation, 'content');

        if (empty($validFilename) || empty($validContent)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, [
                'error' => 'Empty validation data',
                'data' => ['filename' => $validFilename, 'content' => $validContent]
            ], 'cron.ssl.prolong.validation');

            throw new Exception("Empty validation data! [$order->item_id]");
        }

        if ($existValidator = SslValidation::findOne(['file_name' => $validFilename])) {
            $existValidator->delete();
        }

        $sslValidation = new SslValidation();
        $sslValidation->pid = $sslCert->pid;
        $sslValidation->file_name = $validFilename;
        $sslValidation->content = $validContent;

        if (!$sslValidation->save(false)) {
            $sslCert->status = SslCert::STATUS_ERROR;
            $sslCert->save(false);

            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, ['error' => 'Error on SslValidation create', 'data' => $sslValidation->getErrors()], 'cron.ssl.prolong.validation');

            throw new Exception("SslValidation does not created! [$order->item_id]");
        }

        if (!$sslCert->save(false)) {
            $sslCert->status = SslCert::STATUS_ERROR;
            $sslCert->save(false);

            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->item_id, ['error' => 'Error on SslCert update', 'data' => $sslCert->getErrors()], 'cron.ssl.prolong.save');

            throw new Exception("SslCert does not updated! [$order->item_id]");
        }

        $order->status = Orders::STATUS_ADDED;
        $order->save(false);

        $sslCert->prolongedNotice();

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
        $currency = ArrayHelper::getValue($orderDetails, 'currency');

        $project = new Project();
        $project->attributes = $projectDefaults;

        $project->act = Project::STATUS_ACTIVE;
        $project->cid = $order->cid;
        $project->site = $domain;
        $project->name = DomainsHelper::idnToUtf8($domain);
        $project->currency_code = is_numeric($currency) ? CurrencyHelper::getCurrencyCodeById($currency) : $currency; // TODO: Remove after full migrate 999 ticket
        $project->paypal_fraud_settings = json_encode(Yii::$app->params['paypal_fraud_settings']);
        $project->dns_status = Project::DNS_STATUS_ALIEN;
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

        ProvidersHelper::makeProvidersOld($domain);

        $additionalService = new AdditionalServices();
        $additionalService->name = $domain;
        $additionalService->type = AdditionalServices::TYPE_INTERNAL;
        $additionalService->auto_order = 1;
        $additionalService->string_name = 1;
        $additionalService->service_view = 1;
        $additionalService->store = 1;
        $additionalService->auto_services = 1;
        $additionalService->start_count = 1;
        $additionalService->generateApiHelp($domain);
        $additionalService->status = AdditionalServices::STATUS_ACTIVE;
        $additionalService->service_description = 1;
        $additionalService->import = 1;
        $additionalService->service_auto_rate = 1;
        $additionalService->provider_rate = 1;
        $additionalService->service_auto_max = 1;
        $additionalService->service_auto_min = 1;
        $additionalService->currency = $order->getCurrency();

        if (!$additionalService->save(false)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $additionalService->getErrors(), 'cron.order.service');
        }

        if ($child) {
            $userService = new UserServices();
            $userService->panel_id = $project->id;
            $userService->provider_id = $project->provider_id;
            $userService->save(false);
        }

        // Create default panel language
        $panelLanguage = new Languages();
        $panelLanguage->panel_id = $project->id;
        $panelLanguage->language_code = 'en';
        $panelLanguage->name = Yii::$app->params['languages']['en'];
        $panelLanguage->position = 1;
        $panelLanguage->visibility = Languages::VISIBILITY_ON;
        $panelLanguage->edited = Languages::EDITED_OFF;
        $panelLanguage->save(false);

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

        $sqlPanelPath = Yii::$app->params['panelSqlPath'];

        // Make Sql dump from panel template db
        if (!DbHelper::makeSqlDump(Yii::$app->params['panelDefaultDatabase'], $sqlPanelPath)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $sqlPanelPath, 'cron.order.make_sql_dump');
        }

        if (DbHelper::existDatabase($project->db)) {
            // Deploy Sql dump to panel db
            if (!DbHelper::dumpSql($project->db, $sqlPanelPath)) {
                $order->status = Orders::STATUS_ERROR;
                ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_PANEL, $project->id, $sqlPanelPath, 'cron.order.deploy_sql_dump');
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
     * @throws yii\base\UnknownClassException
     */
    public static function domain(Orders $order): ?bool
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

        $registrar = BaseDomain::getRegistrarName($domain);

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
        $domainModel->registrar = $registrar;

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
        } else if (!empty($setNss)) {
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
     * Prolongation domain
     * @param Orders $order
     * @return bool
     * @throws Exception
     */
    public static function prolongationDomain(Orders $order)
    {
        $domain = Domains::findOne($order->item_id);

        if (empty($domain)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, [
                'error' => 'Domain not found in database',
                'domain_id' => $order->item_id,
            ], 'cron.prolong.domain.e_domain');

            throw new Exception("Domain [$order->item_id] is not found in database!");
        }

        // Save old Domain data before renew
        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, $domain->attributes, 'cron.prolong.domain.old_data');

        $domainRenewResult = OrderDomainHelper::domainRenew($order);

        if (empty($domainRenewResult) || !empty($domainRenewResult['_error'])) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, [
                'error' => 'Invalid API domainRenew result',
                'api_response' => $domainRenewResult,
            ], 'cron.prolong.domain.e_renew');

            throw new Exception("Domain [$order->item_id] domainRenew action failed! [$order->item_id]");
        }

        // Get domain info after renew action
        $domainInfoResult = OrderDomainHelper::domainGetInfo($order);

        ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, $domainInfoResult, 'cron.prolong.domain_info_after');

        if (empty($domainInfoResult) || !empty($domainInfoResult['_error'])) {
            throw new Exception("Domain [$order->item_id] domainGetInfo returned an incorrect result!");
        }

        $expiryDt = ArrayHelper::getValue($domainInfoResult, 'expires');

        if (empty($expiryDt)) {
            throw new Exception("Domain [$order->item_id] `expiry` is not exist in domain info result after domain renew!");
        }

        $expiryTs = strtotime($expiryDt);

        if (empty($expiryTs)) {
            throw new Exception("Domain [$order->item_id] `expiry` has invalid format!");
        }

        // Renew domain data
        $domain->status = Domains::STATUS_OK;
        $domain->expiry = $expiryTs;
        $domain->setItemDetails($domainRenewResult, 'domain_renew_info');

        if (!$domain->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->item_id, [
                'error' => 'Invalid domain update',
                'errors' => $domain->getErrors(),
            ], 'cron.prolong.domain.e_update');

            throw new Exception("Domain [$order->item_id] update action failed!");
        }

        $order->status = Orders::STATUS_ADDED;
        $order->save(false);

        $domain->prolongedNotice();

        return true;
    }

    /**
     * Create store
     * @param Orders $order
     * @return bool
     */
    public static function store(Orders $order)
    {
        $orderDetails = $order->getDetails();
        $adminEmail = ArrayHelper::getValue($orderDetails, 'admin_email');
        $isTrial = (bool)ArrayHelper::getValue($orderDetails, 'trial', false);

        $projectDefaults = Yii::$app->params['store.defaults'];

        $store = new Stores();
        $store->setAttributes($projectDefaults);

        $store->customer_id = $order->cid;
        $store->currency = ArrayHelper::getValue($orderDetails,'currency');
        $store->domain = DomainsHelper::idnToUtf8($order->domain);
        $store->subdomain = 0;
        $store->name = ArrayHelper::getValue($orderDetails,'name');
        $store->status = Stores::STATUS_ACTIVE;
        $store->trial = $isTrial;
        $store->generateExpired($isTrial);
        $store->dns_status = Stores::DNS_STATUS_ALIEN;

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

        if (!$store->enableDomain()) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $store->id, $store->getErrors(), 'cron.order.store_domain');
        }

        // Create nginx config
        SuperTaskHelper::setTasksNginx($store, [
            'order_id' => $order->id
        ]);

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

        if (DbHelper::existDatabase($store->db_name) && !empty($adminEmail)) {
            Yii::$app->db->createCommand("
                INSERT INTO `{$store->db_name}`.`notification_admin_emails` (`email`, `status`, `primary`) VALUES ('{$adminEmail}', 1, 1);
            ")->execute();
        }

        return true;
    }

    /**
     * Obtain free letsencrypt SSL certificate
     * @param Orders $order
     * @return bool
     * @throws Exception
     */
    public static function freeSsl(Orders $order)
    {
        $orderDetails = $order->getDetails();

        /** @var $project Stores|Project|Sites  */

        switch ($orderDetails['project_type']) {
            case ProjectInterface::PROJECT_TYPE_PANEL:
                $project = Project::findOne($orderDetails['pid']);
                break;

            case ProjectInterface::PROJECT_TYPE_STORE:
                $project = Stores::findOne($orderDetails['pid']);
                break;

            case ProjectInterface::PROJECT_TYPE_GATEWAY:
                $project = Sites::findOne($orderDetails['pid']);
                break;

            default:
                throw new Exception('Project type [' . $orderDetails['project_type'] . '] not exist!');
                break;
        }

        if (!$project) {
            throw new Exception('Project [' . $orderDetails['project_type'] . '] [' . $orderDetails['pid'] . '] not found!');
        }

        // Check if its prolonged GogetSSl -> Letsencrypt or regular Letsencrypt SSL order
        $orderDelay = ArrayHelper::getValue($orderDetails, 'delay', 0);

        if (time() < $order->date + $orderDelay) {

            $order->processing = Orders::PROCESSING_NO;

            if (!$order->save(false)) {
                throw new Exception('Cannot update Ssl order [orderId=' . $order->id . ']');
            };

            return true;
        }

        if (SslCert::findOne([
            'domain' => $order->domain,
            'status' => SslCert::STATUS_ACTIVE
        ])) {
            throw new Exception('Already exist active SSL for domain [' . $order->domain . ']!');
        }

        $sslCertItem = SslCertItem::findOne($orderDetails['ssl_cert_item_id']);

        if (!$sslCertItem) {
            throw new Exception('SslItem for domain [' . $order->domain . '] not found!');
        }

        $ssl = new SslCertLetsencrypt();
        $ssl->cid = $order->cid;
        $ssl->pid = $orderDetails['pid'];
        $ssl->project_type = $orderDetails['project_type'];
        $ssl->item_id = $sslCertItem->id;
        $ssl->status = SslCert::STATUS_PENDING;
        $ssl->checked = SslCert::CHECKED_NO;
        $ssl->domain = $order->domain;

        $letsencrypt = new Letsencrypt();
        $letsencrypt->setStageMode(false);
        $letsencrypt->setPaths(Yii::$app->params['letsencrypt']['paths']);
        $letsencrypt->setSsl($ssl);

        $letsencrypt->issueCert(!(bool)$project->subdomain);

        $ssl->status = SslCertLetsencrypt::STATUS_ACTIVE;
        $ssl->checked = SslCertLetsencrypt::CHECKED_YES;

        if (!$ssl->save(false)) {
            throw new Exception('Cannot create SslCertLetsencrypt [orderId=' . $order->id . ']');
        }

        ThirdPartyLog::log(ThirdPartyLog::ITEM_OBTAIN_LETSENCRYPT_SSL, $order->item_id, $letsencrypt->getExecResult(), 'cron.le-ssl.obtain');

        if (!OrderSslHelper::addDdos($ssl, [
            'site' => $order->domain,
            'crt' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_FULLCHAIN),
            'key' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_KEY),
        ])) {
            throw new Exception('Cannot add SSL to DDoS!');
        }

        if (!OrderSslHelper::addConfig($ssl, [
            'domain' => $order->domain,
            'crt_cert' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_FULLCHAIN),
            'key_cert' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_KEY),
        ])) {
            throw new Exception('Cannot add SSL to Config!');
        }

        $order->status = Orders::STATUS_ADDED;
        $order->item_id = $ssl->id;
        $order->setItemDetails(['expiry_at' => $ssl->expiry], 'ssl_details');

        if (!$order->save(false)) {
            throw new Exception('Cannot update Ssl order [orderId=' . $order->id . ']');
        }

        $project->ssl = Project::SSL_MODE_ON;

        if (!$project->save(false)) {
            throw new Exception('Cannot update project [' . $project->id . ']');
        }

        // Create new unreaded ticket after activate ssl cert.
        // Only for SSL created by user

        $messagePrefix = 'my';

        if($project->hasManualPaymentMethods() && $order->ip != '127.0.0.1' && $order->ip != '') {
            $ticket = new Tickets();
            $ticket->customer_id =$ssl->cid;
            $ticket->is_admin = 1;
            $ticket->subject = Yii::t('app', "ssl.$messagePrefix.created.ticket_subject");
            if ($ticket->save(false)) {
                $ticketMessage = new TicketMessages();
                $ticketMessage->ticket_id = $ticket->id;
                $ticketMessage->admin_id = SuperAdmin::DEFAULT_ADMIN;
                $ticketMessage->created_at = time();
                $ticketMessage->message = Yii::t('app', "ssl.$messagePrefix.created.ticket_message", [
                    'domain' => $project->getBaseDomain()
                ]);
                $ticketMessage->ip = ' ';
                $ticketMessage->user_agent = ' ';
                $ticketMessage->save(false);
            }
        }

        return true;
    }

    /**
     * Renew free Letsencrypt certificate
     * @param Orders $order
     * @return bool
     * @throws Exception
     */
    public static function prolongationFreeSsl(Orders $order)
    {
        $ssl = SslCertLetsencrypt::findOne($order->item_id);

        if (!$ssl) {
            throw new Exception('SslCertLetsencrypt item not found [orderId=' . $order->id . ']');
        }

        $ssl->status = SslCertLetsencrypt::STATUS_INCOMPLETE;
        $ssl->checked = SslCertLetsencrypt::CHECKED_NO;

        if (!$ssl->save(false)) {
            throw new Exception('Cannot update SslCertLetsencrypt item [sslId=' . $ssl->id . ']');
        }

        /** @var $project Stores|Project  */

        switch ($ssl->project_type) {
            case ProjectInterface::PROJECT_TYPE_PANEL:
                $project = Project::findOne($ssl->pid);
                break;

            case ProjectInterface::PROJECT_TYPE_STORE:
                $project = Stores::findOne($ssl->pid);
                break;

            case ProjectInterface::PROJECT_TYPE_GATEWAY:
                $project = Sites::findOne($ssl->pid);
                break;

            default:
                throw new Exception('Project type [' . $ssl->project_type . '] not exist!');
                break;
        }

        if (!$project) {
            throw new Exception('Project [' . $ssl->project_type . '] [' . $ssl->pid . '] not found!');
        }

        $letsencrypt = new Letsencrypt();
        $letsencrypt->setStageMode(false);
        $letsencrypt->setPaths(Yii::$app->params['letsencrypt']['paths']);
        $letsencrypt->setSsl($ssl);

        $letsencrypt->renewCert(!(bool)$project->subdomain);

        $ssl->status = SslCertLetsencrypt::STATUS_ACTIVE;
        $ssl->checked = SslCertLetsencrypt::CHECKED_YES;

        if (!$ssl->save(false)) {
            throw new Exception('Cannot update SslCertLetsencrypt item [sslId=' . $ssl->id . ']');
        }

        ThirdPartyLog::log(ThirdPartyLog::ITEM_RENEW_LETSENCRYPT_SSL, $order->item_id, $letsencrypt->getExecResult(), 'cron.le-ssl.renew');

        if (!OrderSslHelper::addDdos($ssl, [
            'site' => $order->domain,
            'crt' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_FULLCHAIN),
            'key' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_KEY),
        ])) {
            throw new Exception('Cannot add SSL to DDoS!');
        }

        if (!OrderSslHelper::addConfig($ssl, [
            'domain' => $order->domain,
            'crt_cert' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_FULLCHAIN),
            'key_cert' => $ssl->getCsrFile(SslCertLetsencrypt::SSL_FILE_KEY),
        ])) {
            throw new Exception('Cannot add SSL to Config!');
        }

        $order->status = Orders::STATUS_ADDED;
        $order->item_id = $ssl->id;
        $order->setItemDetails(['expiry_at' => $ssl->expiry], 'ssl_details');

        if (!$order->save(false)) {
            throw new Exception('Cannot update Ssl order [orderId=' . $order->id . ']');
        }

        return true;
    }

    /**
     * Create gateway
     * @param Orders $order
     * @return bool
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function gateway(Orders $order)
    {
        $orderDetails = $order->getDetails();

        $projectDefaults = Yii::$app->params['gateway.defaults'];

        $site = new Sites();
        $site->setAttributes($projectDefaults);

        $site->customer_id = $order->cid;
        $site->domain = DomainsHelper::idnToUtf8($order->domain);
        $site->subdomain = 0;
        $site->status = Sites::STATUS_ACTIVE;
        $site->generateExpired();
        $site->dns_status = Sites::DNS_STATUS_ALIEN;

        if (!$site->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_GATEWAY, $order->id, $site->getErrors(), 'cron.order.gateway');
            return false;
        }

        $site->generateDbName();

        if (!$site->save(false)) {
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_GATEWAY, $order->id, $site->getErrors(), 'cron.order.gateway');
            return false;
        }

        $expiredLog = new ExpiredLog();
        $expiredLog->setAttributes([
            'pid' => $site->id,
            'expired_last' => 0,
            'expired' => $site->expired_at,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CREATE_GATEWAY_EXPIRY
        ]);

        $expiredLog->save(false);

        $order->status = Orders::STATUS_ADDED;
        $order->item_id = $site->id;
        $order->save(false);
        $order->refresh();

        $siteAdmin = new Admins();
        $siteAdmin->site_id = $site->id;
        $siteAdmin->username = ArrayHelper::getValue($orderDetails,'username');
        $siteAdmin->password = ArrayHelper::getValue($orderDetails,'password');
        $siteAdmin->status = Admins::STATUS_ACTIVE;

        if (!$siteAdmin->save(false)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_GATEWAY, $site->id, $siteAdmin->getErrors(), 'cron.order.gateway_admin');
        }

        // Create nginx config
        SuperTaskHelper::setTasksNginx($site, [
            'order_id' => $order->id
        ]);

        // Create Store db
        if (!DbHelper::existDatabase($site->db_name)) {
            DbHelper::createDatabase($site->db_name);
        }

        if (!DbHelper::existDatabase($site->db_name)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_GATEWAY, $site->id, '', 'cron.order.gateway_db');
        }

        $storeSqlPath = Yii::$app->params['gatewaySqlPath'];

        // Make Sql dump from store template db
        if (!DbHelper::makeSqlDump(Yii::$app->params['gatewayDefaultDatabase'], $storeSqlPath)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_GATEWAY, $site->id, $storeSqlPath, 'cron.order.make_sql_dump');
        }

        // Deploy Sql dump to store db
        if (!DbHelper::dumpSql($site->db_name, $storeSqlPath)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_GATEWAY, $site->id, $storeSqlPath, 'cron.order.deploy_sql_dump');
        }

        if (!$site->enableDomain()) {
            $order->status = Orders::STATUS_ERROR;
        }

        // Change status
        if (Orders::STATUS_ADDED != $order->status) {
            $order->save(false);
            return false;
        }

        $paypalGatewayMethod = PaymentMethods::findOne(PaymentMethods::METHOD_PAYPAL_GATEWAY);
        if ($paypalGatewayMethod) {
            $projects = Project::find()
                ->andWhere([
                    'cid' => $order->cid,
                    'act' => Project::STATUS_ACTIVE,
                    'child_panel' => 0,
                ])
                ->andWhere("db <> ''")
                ->all();
            foreach ($projects as $project) {
                $currency = PaymentMethodsCurrency::findOne([
                    'currency' => $project->getCurrencyCode(),
                    'method_id' => $paypalGatewayMethod->id,
                ]);

                if (!$currency) {
                    continue;
                }

                $paypalGateway = PanelPaymentMethods::findOne(['panel_id' => $project->id, 'method_id' => $paypalGatewayMethod->id]);

                if (!$paypalGateway) {
                    $paypalGateway = new PanelPaymentMethods();
                    $paypalGateway->currency_id = $currency->id;
                    $paypalGateway->method_id = $paypalGatewayMethod->id;
                    $paypalGateway->panel_id = $project->id;
                    $paypalGateway->name = $paypalGatewayMethod->name;
                    $paypalGateway->setOptions();
                    $paypalGateway->visibility = PanelPaymentMethods::VISIBILITY_DISABLED;
                    $paypalGateway->save(false);
                }
            }
        }


        return true;
    }
}