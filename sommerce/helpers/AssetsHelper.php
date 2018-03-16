<?php
namespace sommerce\helpers;

use common\models\stores\Stores;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AssetsHelper
 * @package sommerce\helpers
 */
class AssetsHelper {

    /**
     * Get unique file url
     * @param string $path
     * @param string $dir
     * @return string
     */
    public static function getFileUrl($path, $dir = "@sommerce/web")
    {
        $filePath = !empty($dir) ? Yii::getAlias($dir . $path) : $path;

        if (file_exists($filePath)) {
            $timestamp = @filemtime($filePath);

            if ($timestamp > 0) {
                $path .= '?v=' . $timestamp;
            }
        }

        return $path;
    }

    /**
     * Get store assets path
     * @return string
     */
    public static function getAssetPath()
    {
        /**
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        return '/assets/' . $store->getFolder();
    }

    /**
     * Get panel assets files list
     * @return array
     */
    public static function getPanelAssets() {
        /**
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();
        $folderContent = $store->getFolderContentData();

        $folder = static::getAssetPath();

        $styles = [];

        $scripts = [];

        $json = [];


        foreach (ArrayHelper::getValue($folderContent, 'css', []) as $filename) {
            $styles[] = [
                'href' => $folder . '/css/' . $filename
            ];
        }

        foreach (ArrayHelper::getValue($folderContent, 'js', []) as $filename) {
            $scripts[] = [
                'src' => $folder . '/js/' . $filename
            ];
        }

        foreach (ArrayHelper::getValue($folderContent, 'json', []) as $filename) {
            $json[] = [
                'src' => $folder . '/json/' . $filename
            ];
        }

        return [
            'scripts' => $scripts,
            'styles' => $styles,
            'json' => $json
        ];
    }
}