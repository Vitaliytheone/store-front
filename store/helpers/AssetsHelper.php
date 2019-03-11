<?php
namespace store\helpers;

use common\models\stores\Stores;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AssetsHelper
 * @package store\helpers
 */
class AssetsHelper {

    static $customScriptFiles = [];

    /**
     * Get unique file url
     * @param string $path
     * @param string $dir
     * @return string
     */
    public static function getFileUrl($path, $dir = "@store/web")
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
     * Get store script files list
     * @return array
     */
    public static function getStoreScripts() {

        $nodePath = Yii::getAlias('@node_modules');

        $scripts= [];

        $asset = Yii::$app->assetManager->publish($nodePath . '/underscore/underscore-min.js');
        if (!empty($asset[1])) {
            $scripts[] = $asset[1];
        }

        foreach (static::$customScriptFiles as $scriptFile) {
            $scripts[] =  $scriptFile;
        }

        $scripts[] = AssetsHelper::getFileUrl('/js/frontend.js');

        return $scripts;
    }

    /**
     * Add custom script file path or url
     * @param string $file
     */
    public static function addCustomScriptFile($file)
    {
        static::$customScriptFiles[] = $file;
    }
}