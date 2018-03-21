<?php

namespace console\controllers\my;

use common\models\panel\PaymentsLog;
use common\models\panels\Domains;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\Logs;
use common\models\panels\MyCustomersHash;
use common\models\panels\Orders;
use common\models\panels\PaymentGateway;
use common\models\panels\PaymentHash;
use common\models\panels\Payments;
use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\Tariff;
use common\models\panels\ThirdPartyLog;
use my\components\Paypal;
use my\helpers\OrderHelper;
use my\helpers\SuperTaskHelper;
use my\mail\mailers\InvoiceCreated;
use my\mail\mailers\PanelExpired;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\base\Module;
use yii\db\Exception as DbException;

/**
 * Class CronController
 * @package console\controllers\my
 */
class CronController extends CustomController
{
    public function __construct($id, Module $module, array $config = [])
    {
        Yii::$app->db->commandClass = '\my\components\db\Command';
        parent::__construct($id, $module, $config);
    }

    /**
     * Execute order
     * @access public
     * @return void
     */
    public function actionExecuteOrder()
    {
        $orders = Orders::find()->andWhere([
            'status' => Orders::STATUS_PAID,
            'processing' => 0,
            'item' => [
                Orders::ITEM_BUY_PANEL,
                Orders::ITEM_BUY_SSL,
                Orders::ITEM_BUY_DOMAIN,
                Orders::ITEM_BUY_CHILD_PANEL
            ]
        ])->all();

        /**
         * @var Orders $order
         */
        foreach ($orders as $order) {
            $order->process();
            try {
                switch ($order->item) {
                    case Orders::ITEM_BUY_SSL:
                        OrderHelper::ssl($order);
                    break;

                    case Orders::ITEM_BUY_PANEL:
                        OrderHelper::panel($order);
                    break;

                    case Orders::ITEM_BUY_DOMAIN:
                        OrderHelper::domain($order);
                    break;

                    case Orders::ITEM_BUY_CHILD_PANEL:
                        OrderHelper::panel($order, true);
                    break;
                }
            } catch (Exception $e) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, $e->getMessage() . $e->getTraceAsString(), 'cron.order.exception');
                $order->status = Orders::STATUS_ERROR;
                $order->save(false);
            } catch(ErrorException $e) {
                ThirdPartyLog::log(ThirdPartyLog::ITEM_ORDER, $order->id, $e->getMessage() . $e->getTraceAsString(), 'cron.order.error_exception');
                $order->status = Orders::STATUS_ERROR;
                $order->save(false);
            }
        }
    }

    /**
     * Execute order
     * @access public
     * @return void
     */
    public function actionSslStatus()
    {
        $sslList = SslCert::find()->andWhere([
            'checked' => SslCert::CHECKED_NO
        ])->all();

        foreach ($sslList as $ssl) {
            OrderHelper::updateSslOrderStatus($ssl);
        }
    }

    /**
     * Create new panel invoice
     * @access public
     * @return void
     */
    public function actionCreateInvoice()
    {
        $date = time() + (7 * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

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
     * Update old frozen panel status
     * @access public
     * @return void
     */
    public function actionTerminatePanel()
    {
        $date = time() - (7 * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        /**
         * @var $order Orders
         */
        foreach (Orders::find()->andWhere('status = :pending AND date < :date', [
            ':pending' => Orders::STATUS_PENDING,
            ':date' => $date // 7 дней; 24 часа; 60 минут; 60 секунд
        ])->all() as $order) {
            $order->cancel();
        }

        $date = strtotime("-1 month", time()); // + 1 месяц

        // Берем по 1 панели на обработку
        $project = Project::find()
            ->leftJoin('logs', 'logs.panel_id = project.id AND logs.type = :type AND logs.created_at > :date', [
                ':date' => $date,
                ':type' => Logs::TYPE_RESTORED
            ])
            ->andWhere([
                'project.act' => Project::STATUS_FROZEN
            ])
            ->andWhere('project.expired < :expired AND logs.id IS NULL', [
                ':expired' => $date
            ])
            ->one();

        /**
         * @var Project $project
         */

        if ($project) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $project->act = Project::STATUS_TERMINATED;

                if ($project->save(false)) {
                    $project->terminate();
                }
            } catch (DbException $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString());
                return;
            }

            $transaction->commit();
        }
    }

    /**
     * Freeze panel
     */
    public function actionFreezePanel()
    {
        $date = time();

        $projects = Project::find()
            ->andWhere([
                'project.act' => Project::STATUS_ACTIVE
            ])
            ->andWhere('project.expired < :expired', [
                ':expired' => $date
            ])
            ->all();

        /**
         * @var Project $project
         */
        foreach ($projects as $project) {
            $project->refresh();

            if ($project->act == Project::STATUS_ACTIVE && $project->expired < $date) {
                $project->changeStatus(Project::STATUS_FROZEN);
            }
        }
    }

    /**
     * Check panel expired
     */
    public function actionPanelExpired()
    {
        $now = time();
        $three = $now + (3 * 24 * 60 * 60); // 3 дня; 24 часа; 60 минут; 60 секунд
        $two = $now + (2 * 24 * 60 * 60); // 2 дня; 24 часа; 60 минут; 60 секунд
        $one = $now + (1 * 24 * 60 * 60); // 1 дня; 24 часа; 60 минут; 60 секунд

        $projects = Project::find()
            ->andWhere([
                'project.act' => Project::STATUS_ACTIVE,
            ])->andWhere('project.expired < :expired', [
                ':expired' => $three
            ])
            ->all();

        foreach ($projects as $project) {
            if ($one >= $project->expired) {
                $days = 1;
            } else if ($two >= $project->expired) {
                $days = 2;
            } else {
                $days = 3;
            }

            $mail = new PanelExpired([
                'project' => $project,
                'days_expired' => $days
            ]);
            $mail->send();
        }
    }

    /**
     * Update old pending payments
     * @access public
     * @return void
     */
    public function actionExpiredPayment()
    {
        $date = time() - (7 * 24 * 60 * 60); // 7 дней; 24 часа; 60 минут; 60 секунд

        // Update old pending payments to expired status
        Payments::updateAll([
            'status' => Payments::STATUS_EXPIRED
        ], 'status = :pending AND date < :date', [
            ':pending' => Payments::STATUS_PENDING,
            ':date' => $date
        ]);
    }

    /**
     * Cron to check payments status with status pending
     */
    public function actionCheckPayments()
    {
        $paypal = new Paypal();

        foreach (Payments::find()->andWhere([
            'type' => PaymentGateway::METHOD_PAYPAL,
            'status' => [
                Payments::STATUS_WAIT,
                Payments::STATUS_REVIEW,
            ],
            'response' => 1,
        ])->andWhere("transaction_id <> ''")->with(['invoice'])->batch() as $payments) {
            foreach ($payments as $payment) {
                /**
                 * @var Payments $payment
                 */
                if (PaymentGateway::METHOD_PAYPAL == $payment->type) {

                    $GetTransactionDetails = $paypal->request('GetTransactionDetails', array(
                        'TRANSACTIONID' => $payment->transaction_id
                    ));

                    $paymentsLogModel = new PaymentsLog();
                    $paymentsLogModel->attributes = [
                        'pid' => $payment->id,
                        'response' => json_encode([
                            'cron.GetTransactionDetails' => $GetTransactionDetails
                        ]),
                        'logs' => json_encode(array_merge($_SERVER, $_POST, $_GET)),
                        'date' => time(),
                        'ip' => ' '
                    ];
                    $paymentsLogModel->save(false);

                    $amount = ArrayHelper::getValue($GetTransactionDetails, 'AMT');
                    $currency = ArrayHelper::getValue($GetTransactionDetails, 'CURRENCYCODE');
                    $status = ArrayHelper::getValue($GetTransactionDetails, 'PAYMENTSTATUS');
                    $status = strtolower($status);

                    // если статус отличается от pending или completed или In-Progress то меняем статус на 3
                    if (!in_array($status, [
                        'completed',
                        'pending',
                        'in-progress'
                    ])) {
                        $payment->status = Payments::STATUS_FAIL;
                        $payment->save(false);
                        continue;
                    }

                    // Проверяемстатус, сумму и валюту
                    if ($status != 'completed' || $amount != $payment->amount || $currency != 'USD') {
                        continue;
                    }
                }

                $payment->complete();

                $paymentHashModel = new PaymentHash();
                $paymentHashModel->load(array('PaymentHash' => array(
                    'hash' => $payment->transaction_id,
                )));
                $paymentHashModel->save();
            }
        }
    }

    public function actionClearUserHash()
    {
        $sessionDuration = time() - (24 * 60 * 60);
        $cookieDuration = time() - (30 * 24 * 60 * 60);

        MyCustomersHash::deleteAll('
        (type = ' . MyCustomersHash::TYPE_REMEMBER . ' AND updated_at < ' . $cookieDuration . ') 
        OR (type = ' . MyCustomersHash::TYPE_NOT_REMEMBER . ' AND updated_at < ' . $sessionDuration . ')');

    }

    /**
     * Run SuperTasks
     */
    public function actionSuperTasks()
    {
        SuperTaskHelper::runTasksNginx();
    }
}