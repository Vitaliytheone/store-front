<?php

namespace control_panel\helpers;

use common\components\letsencrypt\Letsencrypt;
use common\components\models\SslCertLetsencrypt;
use common\helpers\DbHelper;
use common\helpers\IntegrationsHelper;
use common\helpers\SuperTaskHelper;
use common\models\common\ProjectInterface;
use common\models\gateways\Sites;
use common\models\panels\SuperAdmin;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use common\models\stores\StoreAdmins;
use common\models\stores\Stores;
use control_panel\helpers\order\OrderDomainHelper;
use common\models\panels\Domains;
use common\models\panels\ExpiredLog;
use common\models\panels\Project;
use Yii;
use common\models\panels\Orders;
use common\models\panels\SslCert;
use common\models\panels\SslCertItem;
use common\models\panels\ThirdPartyLog;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use control_panel\helpers\order\OrderSslHelper;

/**
 * Class OrderHelper
 * @package control_panel\helpers
 */
class OrderHelper
{
    /**
     * Create domain
     * @param Orders $order
     * @return bool|null
     * @throws yii\base\UnknownClassException
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

        if (empty($contactResult['id'])) {
            $order->makeError();
            return false;
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

        $registrar = DomainsHelper::getRegistrarName($domain);

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
     * @throws Exception
     * @throws \ReflectionException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
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

        if (!IntegrationsHelper::addStoreIntegrations($store->id)) {
            $order->status = Orders::STATUS_ERROR;
            ThirdPartyLog::log(ThirdPartyLog::ITEM_BUY_STORE, $order->id, 'Error adding store integration', 'cron.order.store_integrations');
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

        $sslCert = SslCert::findOne([
            'domain' => $order->domain,
            'status' => SslCert::STATUS_ACTIVE
        ]);
        if ($sslCert) {
            $sslCert->status = SslCert::STATUS_CANCELED;
            $order->processing = Orders::PROCESSING_NO;
            $order->status = Orders::STATUS_PAID;

            if (!$sslCert->save(false) || ! $order->save(false)) {
                throw new Exception('Cannot update Ssl order [orderId=' . $order->id . ']');
            }

            return true;
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
            $ticket->customer_id = $ssl->cid;
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
}