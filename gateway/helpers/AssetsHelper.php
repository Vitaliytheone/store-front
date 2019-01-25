<?php
namespace gateway\helpers;

use common\models\gateways\Sites;
use Yii;

/**
 * Class AssetsHelper
 * @package gateway\helpers
 */
class AssetsHelper {

    static $customScriptFiles = [];

    /**
     * Get unique file url
     * @param string $path
     * @param string $dir
     * @return string
     */
    public static function getFileUrl($path, $dir = "@gateway/web")
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
     * Get gateway assets path
     * @param boolean $fullPath
     * @return string
     */
    public static function getAssetPath($fullPath = false)
    {
        /**
         * @var $gateway Sites
         */
        $gateway = Yii::$app->gateway->getInstance();

        $path = '/assets/' . $gateway->getFolder();

        if ($fullPath) {
            $path = Yii::getAlias('@webroot') . $path;
        }
        return $path;
    }

    /**
     * Get gateway script files list
     * @return array
     */
    public static function getScripts() {

        $nodePath = Yii::getAlias('@node_modules');

        $scripts= [];

        $asset = Yii::$app->assetManager->publish($nodePath . '/underscore/underscore-min.js');
        if (!empty($asset[1])) {
            $scripts[] = $asset[1];
        }

        foreach (static::$customScriptFiles as $scriptFile) {
            $scripts[] = $scriptFile;
        }

        $scripts[] = AssetsHelper::getFileUrl('/js/main.js');

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