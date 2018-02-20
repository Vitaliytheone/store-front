<?php

namespace console\controllers;

use common\models\store\Carts;
use console\components\sender\SenderComponent;
use Yii;
use yii\console\Controller;

/**
 * Class CronController
 * @package console\controllers
 */
class CronController extends Controller
{
    /**
     * Clear cart items
     */
    public function actionClearCartItems()
    {
        Carts::deleteAll('created_at <= :created_at', [
            ':created_at' => time() - 2592000 // 30 days
        ]);
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

       print_r($result);

    }
}
