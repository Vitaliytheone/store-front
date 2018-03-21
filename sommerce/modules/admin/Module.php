<?php

namespace sommerce\modules\admin;

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
    public $controllerNamespace = 'sommerce\modules\admin\controllers';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = 'admin/site/error';
    }
}
