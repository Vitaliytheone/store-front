<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * Class SystemController
 * @package app\commands
 */
class SystemController extends Controller
{
    public function actionClearCache()
    {
        // Clear global cache
        Yii::$app->runAction('cache/flush-all');

        // Clear sommerce twig
        // Clear sommerce and my assets cache
        foreach ([
            Yii::$app->params['sommerce.twig.cachePath'],
            Yii::$app->params['sommerce.assets.cachePath'],
            Yii::$app->params['my.assets.cachePath'],
            Yii::$app->params['store.twig.cachePath'],
            Yii::$app->params['store.assets.cachePath'],
        ] as $path) {
            $path = Yii::getAlias($path);
            if (is_dir($path)) {
                foreach (scandir($path) as $dirPath) {
                    if (in_array($dirPath, ['.', '..'])) {
                        continue;
                    }
                    $dirPath = $path . DIRECTORY_SEPARATOR . $dirPath;

                    if (is_dir($dirPath)) {
                        FileHelper::removeDirectory($dirPath);
                    }
                }
            }

            $this->stderr('Cleared: ' . $path . "\n", Console::FG_GREEN);
        }
    }
}