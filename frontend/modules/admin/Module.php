<?php

namespace frontend\modules\admin;

use Yii;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'frontend\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = 'admin/site/error';
    }
}
