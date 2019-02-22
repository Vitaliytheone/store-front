<?php
namespace sommerce\helpers;

use common\models\store\PageFiles;
use common\models\store\Pages;
use common\models\stores\Stores;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AssetsHelper
 * @package sommerce\helpers
 */
class AssetsHelper {

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
     * Get assets from pages_files
     * @pararm string $value
     * @return string
     */
    public static function getAssets($value)
    {
        /**
         * @var $store Stores
         */
//        $store = Yii::$app->store->getInstance();

        $file = '';
        Yii::debug($value); // todo del

        if (stripos('styles.css', $value) !== false) {
            Yii::debug('Hi1111'); // todo del
            $file = PageFiles::find()->where(['file_name' => 'styles.css'])->one()->content;
        }
        if (stripos('scripts.js', $value) !== false) {
            $file = PageFiles::find()->where(['file_name' => 'scripts.js'])->one()->content;
        }

        Yii::debug($file, 'content'); // todo del
        return $file;
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