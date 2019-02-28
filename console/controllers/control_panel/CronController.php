<?php

namespace console\controllers\control_panel;

use common\helpers\InvoiceHelper;
use common\models\panel\PaymentsLog;
use common\models\panels\Domains;
use common\models\panels\InvoiceDetails;
use common\models\panels\Invoices;
use common\models\panels\MyCustomersHash;
use common\models\panels\Orders;
use common\models\panels\Params;
use common\models\panels\PaymentHash;
use common\models\panels\Payments;
use common\models\panels\SslCert;
use common\models\panels\ThirdPartyLog;
use common\models\sommerces\Stores;
use console\components\crons\CronFreeSslOrder;
use console\components\payments\PaymentsFee;
use console\components\terminate\TerminateSommerce;
use control_panel\components\payments\Paypal;
use control_panel\helpers\OrderHelper;
use common\helpers\SuperTaskHelper;
use control_panel\helpers\PaymentsHelper;
use control_panel\mail\mailers\PaypalVerificationNeeded;
use console\components\terminate\CancelOrder;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class CronController
 * @package console\controllers\control_panel
 */
class CronController extends CustomController
{

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        Yii::$app->db->commandClass = '\control_panel\components\db\Command';
    }

    /**
     * Execute order
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function actionExecuteOrder()
    {
        $orders = Orders::find()->andWhere([
            'status' => Orders::STATUS_PAID,
            'processing' => Orders::PROCESSING_NO,
            'item' => [
                Orders::ITEM_BUY_SSL,
                Orders::ITEM_BUY_DOMAIN,
                Orders::ITEM_BUY_SOMMERCE,
                Orders::ITEM_PROLONGATION_SSL,
                Orders::ITEM_PROLONGATION_DOMAIN,
                Orders::ITEM_FREE_SSL,
                Orders::ITEM_PROLONGATION_FREE_SSL,
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

                    case Orders::ITEM_BUY_DOMAIN:
                        OrderHelper::domain($order);
                        break;

                    case Orders::ITEM_BUY_SOMMERCE:
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

                    case Orders::ITEM_FREE_SSL:
                        if (Yii::$app->params['free_ssl.create']) {
                            OrderHelper::freeSsl($order);
                        }
                        break;

                    case Orders::ITEM_PROLONGATION_FREE_SSL:
                        if (Yii::$app->params['free_ssl.prolong']) {
                            OrderHelper::prolongationFreeSsl($order);
                        }
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
     * @throws \yii\db\Exception
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
     * @throws \console\components\crons\exceptions\CronException
     */
    public function actionCreateInvoice()
    {
        InvoiceHelper::prolongDomains();
        InvoiceHelper::prolongStores();

        if (Yii::$app->params['free_ssl.prolong']) {
            InvoiceHelper::prolongFreeSsl();
            InvoiceHelper::prolongGogetSsl2LetsencryptSsl();
        }
    }

    /**
     * Update old frozen panel status
     * @access public
     * @return void
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function actionTerminate()
    {
        Yii::$container->get(CancelOrder::class, [
            time() - (7 * 24 * 60 * 60),
            [
                Orders::ITEM_BUY_DOMAIN,
                Orders::ITEM_BUY_SSL,
                Orders::ITEM_BUY_SOMMERCE,
                Orders::ITEM_FREE_SSL,
                Orders::ITEM_PROLONGATION_FREE_SSL,
            ]
        ])->run();

        Yii::$container->get(CancelOrder::class, [
            time() - (30 * 24 * 60 * 60),
            [
                Orders::ITEM_PROLONGATION_SSL,
                Orders::ITEM_PROLONGATION_DOMAIN,
            ]
        ])->run();

        Yii::$container->get(TerminateSommerce::class, [
            strtotime("-1 month", time())
        ])->run();
    }

    /**
     * Freeze panel
     */
    public function actionFreezePanel()
    {
        $date = time();

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
            if ($store->checkExpired()) {
                $store->status = Stores::STATUS_FROZEN;
                $store->save(false);
            }
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
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
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

    /**
     * Clear user hash
     */
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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function actionPaymentsFee()
    {
        Yii::$container->get(PaymentsFee::class, [
            Yii::$app->params['cron.check_payments_fee_days'], // days
        ])->run();
    }

    /**
     * New panel|store Letsencrypt SSL order maker
     * @throws Exception
     */
    public function actionNewSslOrder()
    {
        if (Yii::$app->params['free_ssl.create']) {
            $cron = new CronFreeSslOrder();
            $cron->setConsole($this);
            $cron->setDebug(true);
            $cron->run();
        }
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionUpdateDomainExpiry()
    {
        $domains = Domains::find()
            ->where(['<', 'expiry', time()])
            ->andWhere(['status' => Domains::STATUS_OK])
            ->all();

        foreach ($domains as $domain) {
            $transaction = Yii::$app->db->beginTransaction();
            $domain->status = Domains::STATUS_EXPIRED;

            if (!$domain->save()) {
                $transaction->rollBack();
                continue;
            }

            $orders = Orders::find()->andWhere([
                'item_id' => $domain->id,
                'item' => Orders::ITEM_PROLONGATION_DOMAIN,
                'status' => Orders::STATUS_PENDING,
            ])->all();

            /**
             * @val Orders $order
             */
            foreach ($orders as $order) {
                $invoiceDetails = InvoiceDetails::findOne(['item_id' => $order->id, 'item' => InvoiceDetails::ITEM_PROLONGATION_DOMAIN]);
                if (!$invoiceDetails) {
                    continue;
                }
                $invoiceId = $invoiceDetails->invoice_id;

                try {
                    InvoiceDetails::deleteAll(['invoice_id' => $invoiceId]);
                    Invoices::deleteAll(['id' => $invoiceId]);
                    $order->delete();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    continue;
                }
            }

            $transaction->commit();
        }
    }
}
