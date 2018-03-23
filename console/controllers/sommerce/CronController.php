<?php

namespace console\controllers\sommerce;

use common\models\stores\StoreAdminsHash;
use console\components\getstatus\GetstatusComponent;
use console\components\sender\SenderComponent;
use console\helpers\SommerceHelper;
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
        SommerceHelper::clearStoresCarts(30);
    }

    /**
     * Orders sender & processor
     */
    public function actionSender()
    {
       $sender = new SenderComponent([
           'ordersLimit' => Yii::$app->params['senderOrdersLimit'],
       ]);
       $sender->setConnection(Yii::$app->storeDb);
       $result = $sender->run();

       echo 'Send orders result' . PHP_EOL;
       print_r($result, 0);
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
        StoreAdminsHash::deleteOld();
    }
}
