<?php
namespace gateway\helpers;

use common\models\gateway\ThemesFiles;
use common\models\gateways\Sites;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ThemesHelper
 * @package gateway\helpers
 */
class ThemesHelper {

    /**
     * Get view path by theme uses view name
     * @param string $view
     * @param string $defaultExtension
     * @return string|null
     */
    public static function getView($view, $defaultExtension = 'php')
    {
        $view = ltrim($view, '/');

        $sp = DIRECTORY_SEPARATOR;

        /**
         * @var $site Sites
         */
        $site = Yii::$app->gateway->getInstance();

        $themeFolder = $site->getThemeFolder();

        $viewsPath = Yii::getAlias('@gateway' . DIRECTORY_SEPARATOR . 'views');

        $standard = $sp . 'themes' . $sp . 'default' . $sp . $themeFolder . $sp . $view;

        $standardPath = $viewsPath . $standard;
        $customFiles = ArrayHelper::map($site->getThemeFiles(), 'name', 'content');
        if (!empty($customFiles[$view])) {
            return $customFiles[$view];
        } else if (is_file($standardPath) || is_file($standardPath . '.' . $defaultExtension) || is_file($standardPath . '.php')) {
            return $standard;
        }

        return null;
    }
}