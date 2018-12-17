<?php
namespace gateway\helpers;

use common\models\gateways\Sites;
use Yii;

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

        $custom = $sp . 'themes' . $sp . 'custom' . $sp . $site->id . $sp . $themeFolder . $sp . $view;
        $standard = $sp . 'themes' . $sp . 'default' . $sp . $themeFolder . $sp . $view;

        $customPath = $viewsPath . $custom;
        $standardPath = $viewsPath . $standard;

        if (is_file($customPath) || is_file($customPath . '.' . $defaultExtension) || is_file($customPath . '.php')) {
            return $custom;
        } else if (is_file($standardPath) || is_file($standardPath . '.' . $defaultExtension) || is_file($standardPath . '.php')) {
            return $standard;
        }

        return null;
    }
}