<?php

namespace admin;

use Yii;
use yii\base\Module as BaseModule;

/**
 * admin module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'admin\controllers';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = 'admin/site/error';
    }
}
