<?php

namespace common\helpers;

use common\models\stores\Stores;
use Yii;

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
    public static function getView($view, $defaultExtension = 'twig')
    {
        $view = ltrim($view, '/');
        $sp = DIRECTORY_SEPARATOR;
        $viewsPath = Yii::getAlias('@store' . DIRECTORY_SEPARATOR . 'views');
        $rootPath = $viewsPath . $sp . $view;

        if (is_file($rootPath) || is_file($rootPath . '.' . $defaultExtension) || is_file($rootPath . '.php')) {
            return $rootPath;
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