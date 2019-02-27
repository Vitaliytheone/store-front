<?php

namespace sommerce\helpers;

use Yii;

/**
 * Class AssetsHelper
 * @package sommerce\helpers
 */
class AssetsHelper
{

    static $customScriptFiles = [];

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
     * Get assets from pages_files
     * @pararm string $value
     * @param string $value
     * @return string
     */
    public static function getAssets($value)
    {

        return PageFilesHelper::generateFileVersionLink($value);
    }

    /**
     * Get store script files list
     * @return array
     */
    public static function getStoreScripts()
    {

        $nodePath = Yii::getAlias('@node_modules');

        $scripts = [];

        $asset = Yii::$app->assetManager->publish($nodePath . '/underscore/underscore-min.js');
        if (!empty($asset[1])) {
            $scripts[] = $asset[1];
        }

        foreach (static::$customScriptFiles as $scriptFile) {
            $scripts[] = $scriptFile;
        }

        $scripts[] = static::getFileUrl('/js/frontend.js');

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