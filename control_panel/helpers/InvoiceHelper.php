<?php

namespace control_panel\helpers;

use Yii;
use common\models\sommerces\Stores;
use common\models\sommerces\InvoiceDetails;
use common\models\sommerces\Invoices;
use control_panel\mail\mailers\InvoiceCreated;
use common\models\sommerces\Domains;
use common\models\sommerces\Orders;
use common\models\sommerces\ThirdPartyLog;
use common\models\sommerces\SslCert;
use common\models\sommerces\SslCertItem;
use common\models\common\ProjectInterface;
use console\components\crons\exceptions\CronException;

/**
 * Class InvoiceHelper
 * @package control_panel\helpers
 */
class InvoiceHelper
{
    /**
     * Create invoices to prolong stores
     * @throws \yii\db\Exception
     */
    public static function prolongStores()
    {
        $date = time() + (Yii::$app->params['store.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $stores = Stores::find()
            ->leftJoin(DB_SOMMERCES . '.invoice_details', 'invoice_details.item_id = stores.id AND invoice_details.item = ' . InvoiceDetails::ITEM_PROLONGATION_STORE)
            ->leftJoin(DB_SOMMERCES . '.invoices', 'invoices.id = invoice_details.invoice_id AND invoices.status = ' . Invoices::STATUS_UNPAID)
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

    /**
     * Create invoices to prolong domains
     * @throws \yii\db\Exception
     */
    public static function prolongDomains()
    {
        $date = time() + (Yii::$app->params['domain.invoice_prolong'] * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        $domains = Domains::find()
            ->leftJoin(['orders' => Orders::tableName()], 'orders.item_id = domains.id AND orders.item = :order_item 
                AND orders.status NOT IN (:added, :canceled) ', [
                ':order_item' => Orders::ITEM_PROLONGATION_DOMAIN,
                ':added' => Orders::STATUS_ADDED,
                ':canceled' => Orders::STATUS_CANCELED
            ])
            ->leftJoin(['invoice_details' => InvoiceDetails::tableName()], 'invoice_details.item_id = orders.id AND invoice_details.item = :invoice_item', [
                ':invoice_item' => InvoiceDetails::ITEM_PROLONGATION_DOMAIN
            ])
            ->leftJoin(['invoices' => Invoices::tableName()], 'invoices.id = invoice_details.invoice_id AND invoices.status = :status', [
                ':status' => Invoices::STATUS_UNPAID
            ])
            ->andWhere([
                'domains.status' => Domains::STATUS_OK,
            ])
            ->andWhere('domains.expiry < :expiry', [
                ':expiry' => $date
            ])
            ->groupBy('domains.id')
            ->having("COUNT(orders.id) = 0")
            ->andHaving("COUNT(invoices.id) = 0")
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
     * Prolongation Letsencrypt SSL order maker
     * @throws CronException
     * @throws \yii\db\Exception
     */
    public static function prolongFreeSsl()
    {
        $time = ExpiryHelper::days(Yii::$app->params['letsencrypt']['prolong.days.before']);

        $storesSslCerts = SslCert::find()
            ->leftJoin(['orders' => Orders::tableName()], 'orders.domain = ssl_cert.domain AND orders.item = :order_item AND orders.processing = :orderProcessing', [
                ':order_item' => Orders::ITEM_PROLONGATION_FREE_SSL,
                ':orderProcessing' => Orders::PROCESSING_NO,
            ])
            ->leftJoin(['ssl_cert_item' => SslCertItem::tableName()], 'ssl_cert_item.id = ssl_cert.item_id')
            ->andWhere([
                'ssl_cert.project_type' => ProjectInterface::PROJECT_TYPE_STORE,
            ])
            ->andWhere([
                'ssl_cert.status' => SslCert::STATUS_ACTIVE,
                'ssl_cert_item.provider' => SslCertItem::PROVIDER_LETSENCRYPT,
            ])
            ->andWhere('ssl_cert.expiry_at_timestamp < :expiry', [':expiry' => $time])
            ->groupBy('ssl_cert.id')
            ->having("COUNT(orders.id) = 0")
            ->asArray()
            ->all();

        $activeStoresIds = Stores::find()
            ->andWhere([
                'id' => array_column($storesSslCerts, 'pid'),
                'status' => Stores::STATUS_ACTIVE,
            ])
            ->asArray()
            ->column();

        $storesSslCerts = array_filter($storesSslCerts, function($sslCert) use ($activeStoresIds) {
            return in_array($sslCert['pid'], $activeStoresIds);
        });

        $sslCerts = $storesSslCerts;

        $transaction = Yii::$app->db->beginTransaction();

        SslCert::updateAll(['status' => SslCert::STATUS_RENEWED], ['id' => array_column($sslCerts, 'id')]);

        foreach ($sslCerts as $ssl) {

            $order = new Orders();
            $order->cid = $ssl['cid'];
            $order->status = Orders::STATUS_PAID;
            $order->hide = Orders::HIDDEN_OFF;
            $order->processing = Orders::PROCESSING_NO;
            $order->domain = $ssl['domain'];
            $order->ip = '127.0.0.1';
            $order->item = Orders::ITEM_PROLONGATION_FREE_SSL;
            $order->item_id = $ssl['id'];
            $order->setDetails([
                'pid' => $ssl['pid'],
                'project_type' => $ssl['project_type'],
                'domain' => $ssl['domain'],
                'ssl_cert_item_id' => $ssl['item_id'],
            ]);

            if (!$order->save(false)) {
                throw new CronException('Cannot create order!');
            }
        }

        $transaction->commit();
    }
}