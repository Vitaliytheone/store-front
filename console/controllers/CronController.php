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


    public function actionSender()
    {
       $sender = new SenderComponent();
       $sender->run();
    }
}
