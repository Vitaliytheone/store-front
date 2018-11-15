<?php

namespace console\controllers\my;

use common\helpers\InvoiceHelper;
use common\models\common\ProjectInterface;
use common\models\panel\PaymentsLog;
use common\models\panels\Logs;
use common\models\panels\MyCustomersHash;
use common\models\panels\Orders;
use common\models\panels\Params;
use common\models\panels\PaymentHash;
use common\models\panels\Payments;
use common\models\panels\Project;
use common\models\panels\SslCert;
use common\models\panels\ThirdPartyLog;
use common\models\stores\Stores;
use common\models\panels\AdditionalServices;
use console\components\crons\CronPanelLeSslOrder;
use console\components\payments\PaymentsFee;
use my\components\payments\Paypal;
use my\helpers\OrderHelper;
use common\helpers\SuperTaskHelper;
use my\helpers\PaymentsHelper;
use my\mail\mailers\PanelExpired;
use my\mail\mailers\PaypalVerificationNeeded;
use sommerce\helpers\StoreHelper;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\db\Exception as DbException;
use console\helpers\UpdateServicesCountHelper;

/**
 * Class CronController
 * @package console\controllers\my
 */
class CronController extends CustomController
{

    /** @inheritdoc */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        Yii::$app->db->commandClass = '\my\components\db\Command';
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
            'processing' => Orders::PROCESSING_NO,
            'item' => [
                Orders::ITEM_BUY_PANEL,
                Orders::ITEM_BUY_SSL,
                Orders::ITEM_BUY_DOMAIN,
                Orders::ITEM_BUY_CHILD_PANEL,
                Orders::ITEM_BUY_STORE,
                Orders::ITEM_PROLONGATION_SSL,
                Orders::ITEM_PROLONGATION_DOMAIN,
                Orders::ITEM_OBTAIN_LE_SSL,
                Orders::ITEM_PROLONGATION_LE_SSL,
            ]
        ])->all();

        /**
         * @var Orders $order
         */
        foreach ($orders as $order) {
            $order->process();
            $orderDetails = $order->getDetails();
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

                    case Orders::ITEM_BUY_STORE:
                        // Создаем триальный магазин сразу
                        $isTrial = (bool)ArrayHelper::getValue($orderDetails, 'trial', false);
                        if ($isTrial) {
                            continue;
                        }

                        OrderHelper::store($order);
                    break;

                    case Orders::ITEM_PROLONGATION_SSL:
                        OrderHelper::prolongationSsl($order);
                    break;

                    case Orders::ITEM_PROLONGATION_DOMAIN:
                        OrderHelper::prolongationDomain($order);
                    break;

                    case Orders::ITEM_OBTAIN_LE_SSL:
                        OrderHelper::leSsl($order);
                    break;

                    case Orders::ITEM_PROLONGATION_LE_SSL:
                        OrderHelper::leProlongationSsl($order);
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

        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        Yii::$app->db->createCommand('SET SESSION interactive_timeout = 28800;')->execute();

        foreach ($sslList as $ssl) {
            OrderHelper::updateSslOrderStatus($ssl);
        }
    }

    /**
     * Create prolongation invoices
     * @access public
     * @return void
     */
    public function actionCreateInvoice()
    {
        InvoiceHelper::prolongPanels();
        InvoiceHelper::prolongDomains();
        InvoiceHelper::prolongStores();
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
            ->leftJoin('logs', 'logs.panel_id = project.id AND logs.project_type = :project_type AND logs.type = :type AND logs.created_at > :date', [
                ':project_type' => ProjectInterface::PROJECT_TYPE_PANEL,
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

        StoreHelper::terminateOneStore($date);
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

        $stores = Stores::find()
            ->andWhere([
                'status' => Stores::STATUS_ACTIVE,
            ])
            ->andWhere('expired < :currentTime', [
                ':currentTime' => $date
            ])
            ->all();

        /** @var Stores $store */
        foreach ($stores as $store) {
            $store->refresh();
            $store->checkExpired();
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
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        Yii::$app->db->createCommand('SET SESSION interactive_timeout = 28800;')->execute();

        $paypal = new Paypal();

        foreach (Payments::find()->andWhere([
            'payment_method' => Params::CODE_PAYPAL,
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
                if (Params::CODE_PAYPAL == $payment->payment_method) {

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

                    $payment->fee = ArrayHelper::getValue($GetTransactionDetails, 'FEEAMT');
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

                    $payerId =  ArrayHelper::getValue($GetTransactionDetails, 'PAYERID');
                    $payerEmail = ArrayHelper::getValue($GetTransactionDetails, 'EMAIL');

                    // Paypal payment email verification
                    if (!PaymentsHelper::validatePaypalPayment($payment, $payerId, $payerEmail)) {
                        $code = $payment->verification($payerId, $payerEmail);

                        if ($code && filter_var($payerEmail, FILTER_VALIDATE_EMAIL)) {
                            $mail = new PaypalVerificationNeeded([
                                'payment' => $payment,
                                'email' => $payerEmail,
                                'code' => $code
                            ]);
                            $mail->send();
                        }

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
        (remember = ' . MyCustomersHash::TYPE_REMEMBER . ' AND updated_at < ' . $cookieDuration . ') 
        OR (remember = ' . MyCustomersHash::TYPE_NOT_REMEMBER . ' AND updated_at < ' . $sessionDuration . ')');

    }

    /**
     * Run SuperTasks
     */
    public function actionSuperTasks()
    {
        SuperTaskHelper::runTasks();
    }

    /**
     * Refund all unverified payments
     */
    public function actionRefundPayments()
    {
        PaymentsHelper::refundPaypalVerifyExpiredPayments();
    }

    /**
     * Add fee to all payments by 2 days
     */
    public function actionPaymentsFee()
    {
        Yii::$container->get(PaymentsFee::class, [
            Yii::$app->params['cron.check_payments_fee_days'], // days
        ])->run();
    }

    /**
     * Update service_count & service_inuse_count in additional_services
     */
    public function actionUpdateServicesCount()
    {
        $providers = UpdateServicesCountHelper::buildQuery();

        $providersPanels = UpdateServicesCountHelper::getProviderPanels();

        foreach ($providers as $key => $provider) {
            $projects = ArrayHelper::getValue($providersPanels, $provider['res'], []);
            $usedProjects = [];

            foreach ($projects as $project) {
                if (!empty($project['providers'][$provider['res']])) {
                    $usedProjects[] = $project;
                }
            }

            $service = AdditionalServices::find()
                ->where(['res' => $provider['res']])
                ->one();
            $service->service_count = count(array_values($projects));
            $service->service_inuse_count = count(array_values($usedProjects));
            $service->update();
        }
    }

    /**
     *  New panel ns-checker, order-maker
     * @throws Exception
     */
    public function actionPanelNewSslOrder()
    {
        $cron = new CronPanelLeSslOrder();
        $cron->setConsole($this);
        $cron->setDebug(true);
        $cron->run();
    }
}
