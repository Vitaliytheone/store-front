<?php

namespace console\controllers\sommerce;

use common\events\Events;
use common\models\store\Checkouts;
use common\models\stores\StoreAdminsHash;
use common\models\stores\Stores;
use console\components\getstatus\GetstatusComponent;
use console\components\sender\SenderComponent;
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
                    }
                }
            }
        }
    }
}
