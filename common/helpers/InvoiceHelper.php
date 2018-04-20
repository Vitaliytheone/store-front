<?php
namespace common\helpers;

use common\models\panels\Domains;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\Tariff;
use common\models\stores\Stores;
use my\mail\mailers\InvoiceCreated;
use Yii;

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

            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $project->cid;
            $invoice->total = $tariff->price;
            $invoice->generateCode();
            $invoice->daysExpired(7);

            if ($invoice->save()) {
                $invoiceDetailsModel = new InvoiceDetails();
                $invoiceDetailsModel->invoice_id = $invoice->id;
                $invoiceDetailsModel->item_id = $project->id;
                $invoiceDetailsModel->amount = $invoice->total;
                $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_PANEL;

                if ($project->child_panel) {
                    $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL;
                }

                if (!$invoiceDetailsModel->save()) {
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
            }
        }
    }

    /**
     * Create invoices to prolong domains
     */
    public static function prolongDomains()
    {
        $date = time() + (Yii::$app->params['domain.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $domains = Domains::find()
            ->leftJoin('invoice_details', 'invoice_details.item_id = domains.id AND invoice_details.item = ' . InvoiceDetails::ITEM_PROLONGATION_DOMAIN)
            ->leftJoin('invoices', 'invoices.id = invoice_details.invoice_id AND invoices.status = ' . Invoices::STATUS_UNPAID)
            ->andWhere([
                'domains.status' => Domains::STATUS_OK,
            ])->andWhere('domains.expiry < :expiry', [
                ':expiry' => $date
            ])
            ->groupBy('domains.id')
            ->having("COUNT(invoices.id) = 0")
            ->all();

        foreach ($domains as $domain) {
            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $domain->customer_id;
            $invoice->total = $domain->zone->price_renewal;
            $invoice->generateCode();
            $invoice->daysExpired(7);

            if ($invoice->save()) {
                $invoiceDetailsModel = new InvoiceDetails();
                $invoiceDetailsModel->invoice_id = $invoice->id;
                $invoiceDetailsModel->item_id = $domain->id;
                $invoiceDetailsModel->amount = $invoice->total;
                $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_DOMAIN;

                if (!$invoiceDetailsModel->save()) {
                    continue;
                }

                $transaction->commit();

                $mail = new InvoiceCreated([
                    'domain' => $domain
                ]);
                $mail->send();
            }
        }
    }

    /**
     * Create invoices to prolong ssl
     */
    public static function prolongSsl()
    {
        $date = time() + (Yii::$app->params['ssl.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $sslCerts = SslCert::find()
            ->leftJoin('invoice_details', 'invoice_details.item_id = ssl_cert.id AND invoice_details.item = ' . InvoiceDetails::ITEM_PROLONGATION_SSL)
            ->leftJoin('invoices', 'invoices.id = invoice_details.invoice_id AND invoices.status = ' . Invoices::STATUS_UNPAID)
            ->andWhere([
                'ssl_cert.status' => SslCert::STATUS_ACTIVE,
            ])->andWhere('UNIX_TIMESTAMP(ssl_cert.expiry) < :expiry', [
                ':expiry' => $date
            ])
            ->groupBy('ssl_cert.id')
            ->having("COUNT(invoices.id) = 0")
            ->all();

        foreach ($sslCerts as $ssl) {
            $transaction = Yii::$app->db->beginTransaction();

            $invoice = new Invoices();
            $invoice->cid = $ssl->cid;
            $invoice->total = $ssl->item->price;
            $invoice->generateCode();
            $invoice->daysExpired(7);

            if ($invoice->save()) {
                $invoiceDetailsModel = new InvoiceDetails();
                $invoiceDetailsModel->invoice_id = $invoice->id;
                $invoiceDetailsModel->item_id = $ssl->id;
                $invoiceDetailsModel->amount = $invoice->total;
                $invoiceDetailsModel->item = InvoiceDetails::ITEM_PROLONGATION_SSL;

                if (!$invoiceDetailsModel->save()) {
                    continue;
                }

                $transaction->commit();

                $mail = new InvoiceCreated([
                    'ssl' => $ssl
                ]);
                $mail->send();
            }
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