<?php
namespace common\helpers;

use Yii;
use common\models\stores\Stores;

/**
 * Class ThemesHelper
 * @package common\helpers
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
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        $themeFolder = $store->getThemeFolder();

        $viewsPath = Yii::getAlias('@frontend' . DIRECTORY_SEPARATOR . 'views');

        $custom = $sp . 'themes' . $sp . 'custom' . $sp . $store->id . $sp . $themeFolder . $sp . $view;
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

    /**
     * Get available includes
     * @return array
     */
    public static function getAvailableIncludes()
    {
        return [
            'slider.twig',
            'features.twig',
            'reviews.twig',
            'process.twig',
        ];
    }

    /**
     * Get enabled twig includes
     * @return array
     */
    public static function getEnabledIncludes()
    {
        $enabledList = [];

        /**
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        if ($store->block_slider) {
            $enabledList[] = 'slider.twig';
        }
        if ($store->block_process) {
            $enabledList[] = 'process.twig';
        }
        if ($store->block_features) {
            $enabledList[] = 'features.twig';
        }
        if ($store->block_reviews) {
            $enabledList[] = 'reviews.twig';
        }

        return $enabledList;
    }
}