<?php

namespace sommerce\controllers;

use common\components\MainController;
use common\models\stores\Stores;
use Yii;

/**
 * Custom controller for the Frontend and Admin module
 */
class CommonController extends MainController
{
    /**
     * @var $store Stores
     */
    public $store = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $store = Yii::$app->store->getInstance();

        if (!$store || !($store instanceof Stores)) {
            exit;
        }

        $store->checkExpired();
        $this->store = $store;
    }
}
