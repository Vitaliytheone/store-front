<?php
namespace frontend\controllers;

use common\components\MainController;
use common\models\stores\Stores;
use Yii;

/**
 * Custom controller for the Frontend and Admin module
 */
class CommonController extends MainController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $store = Yii::$app->store->getInstance();

        if (!$store || !($store instanceof Stores)) {
            exit;
        }

        $store->checkExpired();
    }
}
