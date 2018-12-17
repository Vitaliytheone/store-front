<?php

namespace gateway\modules\admin;

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
    public $controllerNamespace = 'gateway\modules\admin\controllers';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = 'admin/site/error';
    }
}
