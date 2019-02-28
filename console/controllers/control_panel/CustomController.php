<?php

namespace console\controllers\control_panel;

use console\components\MainController;
use Yii;

/**
 * Class CustomController
 * @package console\controllers\control_panel
 */
class CustomController extends MainController
{
    public function init()
    {
        $this->frontendPath = Yii::getAlias('@control_panel/config');

        Yii::$app->i18n->translations = [
            'yii' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => "@yii/messages",
                'sourceLanguage' => 'en',
            ],
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@control_panel/messages',
                'sourceLanguage' => 'en',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/superadmin' => 'superadmin.php',
                ],
            ],
        ];

        parent::init();
    }
}