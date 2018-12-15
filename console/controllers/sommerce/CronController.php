<?php

namespace console\controllers\sommerce;

use common\events\Events;
use common\helpers\CurrencyHelper;
use common\models\store\Checkouts;
use common\models\store\Payments;
use common\models\stores\PaymentMethods;
use common\models\stores\PaymentMethodsCurrency;
use common\models\stores\StoreAdminsHash;
use common\models\stores\StorePaymentMethods;
use common\models\stores\Stores;
use console\components\getstatus\GetstatusComponent;
use console\components\sender\SenderComponent;
use sommerce\components\payments\methods\Authorize;
use sommerce\components\payments\methods\Paypal;
use sommerce\components\payments\Payment;
use sommerce\helpers\StoresHelper;
use Yii;

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
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        Yii::$app->db->createCommand('SET SESSION interactive_timeout = 28800;')->execute();
        
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
     * @throws \yii\base\UnknownClassException
     */
    protected function _checkAuthorize(Stores $store)
    {
        $method = PaymentMethods::findOne(['method_name' => PaymentMethods::METHOD_AUTHORIZE]);
        $paymentCurrency = PaymentMethodsCurrency::findOne([
            'method_id' => $method->id,
            'currency' => $store->currency
        ]);

        $paymentMethod = StorePaymentMethods::findOne([
            'method_id' => $method->id,
            'store_id' => $store->id,
            'currency_id' => $paymentCurrency->id,
            'visibility' => StorePaymentMethods::VISIBILITY_ENABLED,
        ]);

        if (!$paymentMethod) {
            return;
        }

        /**
         * @var Authorize $component
         */
        $component = Payment::getPayment($method);

        foreach (Payments::find()->andWhere([
            'method' => $method->method_name,
            'payments.status' => Payments::STATUS_AWAITING,
        ])->batch() as $payments) {
            foreach ($payments as $payment) {
                $component->checkStatus($payment, $store, $paymentMethod);
            }
        }
    }

    /**
     * @param Stores $store
     * @throws \yii\base\UnknownClassException
     */
    protected function _checkPaypalPayment(Stores $store)
    {
        $method = PaymentMethods::findOne(['method_name' => PaymentMethods::METHOD_PAYPAL]);
        $paymentCurrency = PaymentMethodsCurrency::findOne([
            'method_id' => $method->id,
            'currency' => $store->currency
        ]);

        $paymentMethod = StorePaymentMethods::findOne([
            'method_id' => $method->id,
            'store_id' => $store->id,
            'currency_id' => $paymentCurrency->id,
            'visibility' => StorePaymentMethods::VISIBILITY_ENABLED,
        ]);

        // Only for express checkout
        if (!$paymentMethod) {
            return;
        }

        CurrencyHelper::getCurrencyOptions($store->currency);

        /**
         * @var $component Paypal
         */
        $component = Payment::getPayment($method);

        foreach (Payments::find()->andWhere([
            'method' => $method,
            'payments.status' => Payments::STATUS_AWAITING,
        ])->batch() as $payments) {
            foreach ($payments as $payment) {
                $component->checkStatus($payment, $store, $paymentMethod);
            }
        }
    }
}
