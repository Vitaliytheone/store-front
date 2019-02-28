<?php

namespace superadmin;

use Yii;

/**
 * superadmin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'superadmin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->errorHandler->errorAction = Yii::$app->params['superadminUrl'] . '/site/error';
    }
}
