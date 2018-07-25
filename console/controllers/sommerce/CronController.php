<?php

namespace console\controllers\sommerce;

use common\events\Events;
use common\helpers\CurrencyHelper;
use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\stores\PaymentMethods;
use common\models\stores\StoreAdminsHash;
use common\models\stores\Stores;
use console\components\getstatus\GetstatusComponent;
use console\components\sender\SenderComponent;
use sommerce\components\payments\methods\Authorize;
use sommerce\components\payments\methods\Paypal;
use sommerce\components\payments\Payment;
use sommerce\helpers\StoresHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CronController
 * @package console\controllers\sommerce
 */
class CronController extends CustomController
{
    /**
     * Clear cart items
     */
    public function actionClearCartItems()
    {
        StoresHelper::clearStoresCarts(30);
    }

    /**
     * Orders sender & processor
     */
    public function actionSender()
    {
       $sender = new SenderComponent([
           'ordersLimit' => Yii::$app->params['senderOrdersLimit'],
           'apiEndPoint' => Yii::$app->params['localApiDomain'],
       ]);
       $sender->setConnection(Yii::$app->storeDb);
       $sender->run();
    }

    /**
     * Get status
     */
    public function actionGetstatus()
    {
        $getstatus = new GetstatusComponent([
            'ordersLimit' => Yii::$app->params['getstatusOrdersLimit'],
        ]);
        $getstatus->setConnection(Yii::$app->storeDb);
        $getstatus->run();
    }

    /**
     * Clear old admin's auth hashes
     */
    public function actionClearAuth()
    {
        StoreAdminsHash::deleteOld(StoreAdminsHash::MODE_SUPERADMIN_ON, 30 * 60);
        StoreAdminsHash::deleteOld(StoreAdminsHash::MODE_SUPERADMIN_OFF, 30 * 24 * 60 * 60);
    }

    /**
     * Abandoned checkout
     */
    public function actionAbandonedCheckout()
    {
        $storeQuery = Stores::find()->active();
        $checkoutQuery = Checkouts::find()->abandoned();

        /**
         * @var Checkouts $checkout
         */
        foreach ($storeQuery->batch() as $stores) {
            foreach ($stores as $store) {

                // Init store
                Yii::$app->store->setInstance($store);
                foreach ($checkoutQuery->batch() as $checkouts) {
                    foreach ($checkouts as $checkout) {
                        // Send notify
                        Events::add(Events::EVENT_STORE_ABANDONED_CHECKOUT, [
                            'checkout' => $checkout,
                            'store' => $store
                        ]);

                        $checkout->status = Checkouts::STATUS_EXPIRED;
                        $checkout->save(false);
                    }
                }
            }
        }
    }

    /**
     * Cron to check payments status with status pending
     */
    public function actionCheckPayments()
    {
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        Yii::$app->db->createCommand('SET SESSION interactive_timeout = 28800;')->execute();
        
        $storeQuery = Stores::find()->active();

        foreach ($storeQuery->batch() as $stores) {
            foreach ($stores as $store) {

                // Init store
                Yii::$app->store->setInstance($store);

                $this->_checkAuthorize($store);
                $this->_checkPaypalPayment($store);
            }
        }
    }

    /**
     * @param Stores $store
     */
    protected function _checkAuthorize(Stores $store)
    {
        $currencies = ArrayHelper::index(CurrencyHelper::getPaymentsByCurrency($store->currency), 'code');
        $method = ArrayHelper::getValue($currencies, PaymentMethods::METHOD_AUTHORIZE);

        if (!$method) {
            return;
        }

        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_AUTHORIZE,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED,
        ]);

        if (!$paymentMethod) {
            return;
        }

        /**
         * @var Authorize $component
         */
        $component = Payment::getPayment(PaymentMethods::METHOD_AUTHORIZE);

        foreach (Payments::find()->andWhere([
            'method' => PaymentMethods::METHOD_AUTHORIZE,
            'payments.status' => Payments::STATUS_AWAITING,
        ])->batch() as $payments) {
            foreach ($payments as $payment) {
                $component->checkStatus($payment, $store, $paymentMethod);
            }
        }
    }

    /**
     * @param Stores $store
     */
    protected function _checkPaypalPayment(Stores $store)
    {
        $paymentMethod = PaymentMethods::findOne([
            'method' => PaymentMethods::METHOD_PAYPAL,
            'store_id' => $store->id,
            'active' => PaymentMethods::ACTIVE_ENABLED,
        ]);

        // Only for express checkout
        if (!$paymentMethod) {
            return;
        }

        /**
         * @var $component Paypal
         */
        $component = Payment::getPayment('paypal');

        foreach (Payments::find()->andWhere([
            'method' => PaymentMethods::METHOD_PAYPAL,
            'payments.status' => Payments::STATUS_AWAITING,
        ])->batch() as $payments) {
            foreach ($payments as $payment) {
                $component->checkStatus($payment, $store, $paymentMethod);
            }
        }
    }
}
