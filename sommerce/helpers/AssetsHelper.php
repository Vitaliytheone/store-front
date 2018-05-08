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

        $scripts[] = AssetsHelper::getFileUrl('/js/frontend.js');

        return $scripts;
    }
}