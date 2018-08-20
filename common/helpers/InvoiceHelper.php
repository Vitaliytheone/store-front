<?php
namespace common\helpers;

use common\models\common\ProjectInterface;
use common\models\panels\Domains;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\Tariff;
use common\models\panels\Orders;
use common\models\panels\ThirdPartyLog;
use common\models\stores\Stores;
use my\mail\mailers\InvoiceCreated;
use Yii;
use yii\db\Query;

/**
 * Class InvoiceHelper
 * @package common\helpers
 */
class InvoiceHelper
{
    /**
     * Create invoices to prolong panels
     */
    public static function prolongPanels()
    {
        $date = time() + (Yii::$app->params['project.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        /**
         * @var Project $project
         */
        $projects = Project::find()
            ->leftJoin('invoice_details', 'invoice_details.item_id = project.id AND invoice_details.item IN (' . implode(",", [
                    InvoiceDetails::ITEM_PROLONGATION_PANEL,
                    InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL,
                ]) . ')')
            ->leftJoin('invoices', 'invoices.id = invoice_details.invoice_id AND invoices.status = ' . Invoices::STATUS_UNPAID)
            ->andWhere([
                'project.act' => Project::STATUS_ACTIVE,
                'project.no_invoice' => Project::NO_INVOICE_DISABLED
            ])->andWhere('project.expired < :expired', [
                ':expired' => $date
            ])
            ->groupBy('project.id')
            ->having("COUNT(invoices.id) = 0")
            ->all();

        foreach ($projects as $project) {
            $tariff = Tariff::findOne($project->tariff);

            if (!$tariff || !$tariff->price) {
                continue;
            }

            $invoiceDetailsAttributes = [
                'item_id' => $project->id,
                'item' => InvoiceDetails::ITEM_PROLONGATION_PANEL,
            ];

            if ($project->child_panel) {
                $invoiceDetailsAttributes['item'] = InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL;
            }

            // Проверяем наличие уже созданного инвойса на продление
            if ((new Query())
                ->from(InvoiceDetails::tableName() . ' as id')
                ->innerJoin(Invoices::tableName() . ' as i', 'i.id = id.invoice_id AND i.status = ' . Invoices::STATUS_UNPAID)
                ->andWhere($invoiceDetailsAttributes)
                ->exists()) {
                continue;
            }

            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $project->cid;
            $invoice->total = $tariff->price;
            $invoice->generateCode();
            $invoice->daysExpired(7);

            if ($invoice->save()) {
                $invoiceDetailsModel = new InvoiceDetails();
                $invoiceDetailsModel->attributes = $invoiceDetailsAttributes;
                $invoiceDetailsModel->invoice_id = $invoice->id;
                $invoiceDetailsModel->amount = $invoice->total;

                if (!$invoiceDetailsModel->save()) {
                    $transaction->rollBack();
                    continue;
                }

                $transaction->commit();

                if (!$project->child_panel) {
                    if ($project->plan != $project->tariff) {
                        $project->plan = $project->tariff;
                        $project->save(false);
                    }
                }

                $mail = new InvoiceCreated([
                    'project' => $project
                ]);
                $mail->send();

                continue;
            }

            $transaction->rollBack();
        }
    }

    /**
     * Create invoices to prolong domains
     */
    public static function prolongDomains()
    {
        $date = time() + (Yii::$app->params['domain.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $domains = Domains::find()
            ->leftJoin(['orders' => Orders::tableName()], 'orders.item_id = domains.id AND orders.item = :order_item', [
                ':order_item' => Orders::ITEM_PROLONGATION_DOMAIN,
            ])
            ->leftJoin(['invoice_details' => InvoiceDetails::tableName()], 'invoice_details.item_id = orders.id AND invoice_details.item = :invoice_item', [
                ':invoice_item' => InvoiceDetails::ITEM_PROLONGATION_DOMAIN
            ])
            ->leftJoin(['invoices' => Invoices::tableName()], 'invoices.id = invoice_details.invoice_id AND invoices.status = :status', [
                ':status' => Invoices::STATUS_UNPAID
            ])
            ->andWhere([
                'domains.status' => Domains::STATUS_OK,
            ])->andWhere('domains.expiry < :expiry', [
                ':expiry' => $date
            ])
            ->groupBy('domains.id')
            ->having("COUNT(invoices.id) = 0")
            ->all();

        foreach ($domains as $domain) {

            $order = new Orders();
            $order->date = time();
            $order->ip = '127.0.0.1';
            $order->cid = $domain->customer_id;
            $order->item = Orders::ITEM_PROLONGATION_DOMAIN;
            $order->item_id = $domain->id;
            $order->domain = $domain->getDomain();
            $order->setDetails([
                'domain' => $order->getDomain(),
                'details' => $domain->getDetails(),
            ]);

            if (!$order->save(false)) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $order->id, $order->getErrors(), 'cron.create_invoice.order');

                continue;
            }

            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $domain->customer_id;
            $invoice->total = $domain->zone->price_renewal;
            $invoice->generateCode();
            $invoice->daysExpired(30);

            if (!$invoice->save()) {
                $order->status = Orders::STATUS_ERROR;
                $domain->save(false);

                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $invoice->id, $invoice->getErrors(), 'cron.create_invoice.invoice');

                continue;
            }

            $invoiceDetails = new InvoiceDetails();
            $invoiceDetails->invoice_id = $invoice->id;
            $invoiceDetails->item = InvoiceDetails::ITEM_PROLONGATION_DOMAIN;
            $invoiceDetails->item_id = $order->id;
            $invoiceDetails->amount = $invoice->total;

            if (!$invoiceDetails->save()) {
                $order->status = Orders::STATUS_ERROR;
                $domain->save(false);

                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_DOMAIN, $invoiceDetails->id, $invoiceDetails->getErrors(), 'cron.create_invoice.details');

                continue;
            }

            $transaction->commit();

            $mail = new InvoiceCreated([
                'domain' => $domain
            ]);
            $mail->send();
        }
    }

    /**
     * Create invoices and orders to prolong ssl
     */
    public static function prolongSsl()
    {
        $date = time() + (Yii::$app->params['ssl.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $sslCerts = SslCert::find()
        ->leftJoin(['orders' => Orders::tableName()], 'orders.item_id = ssl_cert.id AND orders.item = :order_item ', [
            ':order_item' => Orders::ITEM_PROLONGATION_SSL
        ])
        ->leftJoin(['invoice_details' => InvoiceDetails::tableName()], 'invoice_details.item_id = orders.id AND invoice_details.item = :invoice_item', [
            ':invoice_item' => InvoiceDetails::ITEM_PROLONGATION_SSL
        ])
        ->leftJoin(['invoices' => Invoices::tableName()], 'invoices.id = invoice_details.invoice_id AND invoices.status = :status', [
            ':status' => Invoices::STATUS_UNPAID
        ])
        ->andWhere(['<>','orders.status', Orders::STATUS_ERROR])
        ->andWhere([
            'ssl_cert.status' => SslCert::STATUS_ACTIVE,
        ])
        ->andWhere('UNIX_TIMESTAMP(ssl_cert.expiry) < :expiry', [
            ':expiry' => $date
        ])
        ->groupBy('ssl_cert.id')
        ->having("COUNT(invoices.id) = 0")
        ->all();
        
        foreach ($sslCerts as $ssl) {

            /** @var ProjectInterface $project */
            $project = ProjectHelper::getProjectByType($ssl->project_type, $ssl->pid);

            if (!$project) {
                $ssl->status = SslCert::STATUS_ERROR;
                $ssl->save(false);

                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $ssl->id, "Project not found: project_type[$ssl->project_type], project_id[$ssl->pid]", 'cron.create_invoice.project');

                continue;
            }

            $order = new Orders();
            $order->date = time();
            $order->ip = '127.0.0.1';
            $order->cid = $ssl->cid;
            $order->item = Orders::ITEM_PROLONGATION_SSL;
            $order->item_id = $ssl->id;
            $order->domain = $ssl->domain;
            $order->setDetails([
                'pid' => $ssl->pid,
                'project_type' => $project::getProjectType(),
                'domain' => $ssl->domain,
                'item_id' => $ssl->item_id,
                'details' => $ssl->getAttributes(),
            ]);

            if (!$order->save(false)) {
                $ssl->status = SslCert::STATUS_ERROR;
                $ssl->save(false);

                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $order->id, $order->getErrors(), 'cron.create_invoice.order');

                continue;
            }

            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $ssl->cid;
            $invoice->total = $ssl->item->price;
            $invoice->generateCode();
            $invoice->daysExpired(30);

            if (!$invoice->save()) {
                $ssl->status = SslCert::STATUS_ERROR;
                $ssl->save(false);

                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $invoice->id, $invoice->getErrors(), 'cron.create_invoice');

                continue;
            }

            $invoiceDetails = new InvoiceDetails();
            $invoiceDetails->invoice_id = $invoice->id;
            $invoiceDetails->item = InvoiceDetails::ITEM_PROLONGATION_SSL;
            $invoiceDetails->item_id = $order->id;
            $invoiceDetails->amount = $invoice->total;

            if (!$invoiceDetails->save()) {
                $ssl->status = SslCert::STATUS_ERROR;
                $ssl->save(false);

                ThirdPartyLog::log(ThirdPartyLog::ITEM_PROLONGATION_SSL, $invoiceDetails->id, $invoiceDetails->getErrors(), 'cron.create_invoice.details');

                continue;
            }

            $transaction->commit();

            $mail = new InvoiceCreated([
                'ssl' => $ssl
            ]);
            $mail->send();
        }
    }

    /**
     * Create invoices to prolong stores
     */
    public static function prolongStores()
    {
        $date = time() + (Yii::$app->params['store.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $stores = Stores::find()
            ->leftJoin('invoice_details', 'invoice_details.item_id = stores.id AND invoice_details.item = ' . InvoiceDetails::ITEM_PROLONGATION_STORE)
            ->leftJoin('invoices', 'invoices.id = invoice_details.invoice_id AND invoices.status = ' . Invoices::STATUS_UNPAID)
            ->andWhere([
                'stores.status' => Stores::STATUS_ACTIVE,
            ])->andWhere('stores.expired < :expiry', [
                ':expiry' => $date
            ])
            ->groupBy('stores.id')
            ->having("COUNT(invoices.id) = 0")
            ->all();

        foreach ($stores as $store) {
            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $store->customer_id;
            $invoice->total = Yii::$app->params['storeDeployPrice'];
            $invoice->generateCode();
            $invoice->daysExpired(7);

            if ($invoice->save()) {
                $invoiceDetailsModel = new InvoiceDetails();
                $invoiceDetailsModel->invoice_id = $invoice->id;
                $invoiceDetailsModel->item_id = $store->id;
                $invoiceDetailsModel->amount = $invoice->total;
                $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_STORE;

                if (!$invoiceDetailsModel->save()) {
                    continue;
                }

                $transaction->commit();

                $mail = new InvoiceCreated([
                    'store' => $store
                ]);
                $mail->send();
            }
        }
    }
}