<?php
namespace frontend\helpers;

use Yii;
use common\models\stores\Stores;
use yii\helpers\FileHelper;

/**
 * Class StoreHelper
 * @package frontend\helpers
 */
class StoreHelper {

    /**
     * Get assets path
     * @return bool|string
     */
    public static function getAssetsPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@frontend' . $sp . 'web' . $sp .'assets' . $sp);
    }

    /**
     * Get themes path
     * @return bool|string
     */
    public static function getThemesPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@frontend' . $sp . 'views' . $sp . 'themes' . $sp);
    }

    /**
     * Generate themes assets
     * @param int $id
     * @return bool
     */
    public static function generateAssets($id)
    {
        if (!($store = Stores::findOne($id))) {
            return false;
        }

        $css = $customStyles = $standardStyles = [];
        $js = $customScripts = $standardScripts = [];
        $json = $customJson = $standardJson = [];


        $sp = DIRECTORY_SEPARATOR;

        $assetsPath = static::getAssetsPath();
        $themesPath = static::getThemesPath();

        $themePath = $store->theme_folder;

        $customThemePath = $themesPath . 'custom' . $sp . $store->id . $sp . $themePath . $sp;
        $standardThemePath = $themesPath . 'default' . $sp . $themePath . $sp;
        $assetsPath = $assetsPath . $store->folder . $sp;

        FileHelper::removeDirectory($assetsPath);
        FileHelper::createDirectory($assetsPath);

        if (is_dir($customThemePath)) {
            foreach (FileHelper::findFiles($customThemePath, ['only' => ['*.js']]) as $filePath) {
                $customScripts[basename($filePath)] = $filePath;
            }

            foreach (FileHelper::findFiles($customThemePath, ['only' => ['*.css']]) as $filePath) {
                $customStyles[basename($filePath)] = $filePath;
            }

            foreach (FileHelper::findFiles($customThemePath, ['only' => ['*.json']]) as $filePath) {
                $customJson[basename($filePath)] = $filePath;
            }
        }

        if (is_dir($standardThemePath)) {
            foreach (FileHelper::findFiles($standardThemePath, ['only' => ['*.js']]) as $filePath) {
                $standardScripts[basename($filePath)] = $filePath;
            }

            foreach (FileHelper::findFiles($standardThemePath, ['only' => ['*.css']]) as $filePath) {
                $standardStyles[basename($filePath)] = $filePath;
            }

            foreach (FileHelper::findFiles($standardThemePath, ['only' => ['*.json']]) as $filePath) {
                $standardJson[basename($filePath)] = $filePath;
            }
        }

        if (!empty($standardStyles)) {
            FileHelper::createDirectory($assetsPath . 'css' . $sp);
        }

        if (!empty($standardScripts)) {
            FileHelper::createDirectory($assetsPath . 'js' . $sp);
        }

        if (!empty($standardJson)) {
            FileHelper::createDirectory($assetsPath . 'json' . $sp);
        }

        foreach ($standardStyles as $fileName => $filePath) {
            if (isset($customStyles[$fileName])) {
                $filePath = $customStyles[$fileName];
            }

            if (file_put_contents($assetsPath . 'css' . $sp . $fileName, file_get_contents($filePath))) {
                $css[] = $fileName;
            }
        }

        foreach ($standardScripts as $fileName => $filePath) {
            if (isset($customScripts[$fileName])) {
                $filePath = $customScripts[$fileName];
            }

            if (file_put_contents($assetsPath . 'js' . $sp . $fileName, file_get_contents($filePath))) {
                $js[] = $fileName;
            }
        }

        foreach ($standardJson as $fileName => $filePath) {
            if (isset($customJson[$fileName])) {
                $filePath = $customJson[$fileName];
            }

            if (file_put_contents($assetsPath . 'json' . $sp . $fileName, file_get_contents($filePath))) {
                $json[] = $fileName;
            }
        }

        $store->setFolderContentData([
            'css' => $css,
            'js' => $js,
            'json' => $json
        ]);
    }
}