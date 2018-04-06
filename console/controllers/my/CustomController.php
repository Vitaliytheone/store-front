<?php
namespace console\controllers\my;

use console\components\MainController;
use Yii;

/**
 * Class CustomController
 * @package console\controllers\my
 */
class CustomController extends MainController
{
    public function init()
    {
        $this->frontendPath = Yii::getAlias('@my/config');

        Yii::$app->i18n->translations = [
            'yii' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => "@vendor/yiisoft/yii2/messages",
                'sourceLanguage' => 'en',
            ],
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@my/messages',
                'sourceLanguage' => 'en',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/superadmin' => 'superadmin.php',
                ],
            ],
        ];

        parent::init(); // TODO: Change the autogenerated stub
    }
}