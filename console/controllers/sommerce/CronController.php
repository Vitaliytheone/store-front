<?php

namespace console\controllers\sommerce;

use sommerce\events\Events;
use common\helpers\CurrencyHelper;
use common\models\sommerce\Checkouts;
use common\models\sommerce\Payments;
use common\models\sommerces\PaymentMethods;
use common\models\sommerces\PaymentMethodsCurrency;
use common\models\sommerces\StoreAdminsHash;
use common\models\sommerces\StorePaymentMethods;
use common\models\sommerces\Stores;
use sommerce\components\getstatus\GetstatusComponent;
use sommerce\components\sender\SenderComponent;
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
     * Orders sender & processor
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
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
     * @throws \yii\db\Exception
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
        $checkoutQuery = Checkouts::find()->abandoned()->limit(50);

        /**
         * @var Checkouts $checkout
         */
        foreach ($storeQuery->batch() as $stores) {
            foreach ($stores as $store) {
                // Init store
                Yii::$app->store->setInstance($store);
                foreach ($checkoutQuery->all() as $checkout) {
                    // Send notify
                    Events::add(Events::EVENT_SOMMERCE_ABANDONED_CHECKOUT, [
                        'checkout' => $checkout,
                        'store' => $store
                    ]);

                    $checkout->status = Checkouts::STATUS_EXPIRED;
                    $checkout->save(false);
                }
            }
        }
    }

    /**
     * Cron to check payments status with status pending
     * @throws \yii\base\UnknownClassException
     * @throws \yii\db\Exception
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
        $method = PaymentMethods::findOne(PaymentMethods::METHOD_AUTHORIZE);
        if (!$method) {
            return;
        }

        $paymentCurrency = PaymentMethodsCurrency::findOne([
            'method_id' => $method->id,
            'currency' => $store->currency
        ]);
        if (!$paymentCurrency) {
            return;
        }

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
        $component = Payment::getPayment($method->class_name);

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
        $method = PaymentMethods::findOne(PaymentMethods::METHOD_PAYPAL);
        if (!$method) {
            return;
        }

         $paymentCurrency = PaymentMethodsCurrency::findOne([
            'method_id' => $method->id,
            'currency' => $store->currency
        ]);
        if (!$paymentCurrency) {
            return;
        }

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
        $component = Payment::getPayment($method->class_name);

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
