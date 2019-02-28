<?php
namespace store\helpers;

use common\models\common\ProjectInterface;
use common\models\panels\Logs;
use Yii;
use common\models\stores\Stores;
use yii\helpers\FileHelper;
use common\models\store\CustomThemes;
use yii\db\Exception as DbException;

/**
 * Class StoreHelper
 * @package store\helpers
 */
class StoreHelper {

    /**
     * Get assets path
     * @return bool|string
     */
    public static function getAssetsPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@store' . $sp . 'web' . $sp .'assets' . $sp);
    }

    /**
     * Get themes path
     * @return bool|string
     */
    public static function getThemesPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@store' . $sp . 'views' . $sp . 'themes' . $sp);
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

        $isCustomTheme = strpos($themePath, CustomThemes::THEME_PREFIX) !== false;

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

        if (!empty($standardStyles) || !empty($customStyles)) {
            FileHelper::createDirectory($assetsPath . 'css' . $sp);
        }

        if (!empty($standardScripts) || !empty($customScripts)) {
            FileHelper::createDirectory($assetsPath . 'js' . $sp);
        }

        if (!empty($standardJson) || !empty($customJson)) {
            FileHelper::createDirectory($assetsPath . 'json' . $sp);
        }

        if ($isCustomTheme) {
            foreach ($customStyles as $fileName => $filePath) {
                if (file_put_contents($assetsPath . 'css' . $sp . $fileName, file_get_contents($filePath))) {
                    $css[] = $fileName;
                }
            }

            foreach ($customScripts as $fileName => $filePath) {
                if (file_put_contents($assetsPath . 'js' . $sp . $fileName, file_get_contents($filePath))) {
                    $js[] = $fileName;
                }
            }

            foreach ($customJson as $fileName => $filePath) {
                if (file_put_contents($assetsPath . 'json' . $sp . $fileName, file_get_contents($filePath))) {
                    $json[] = $fileName;
                }
            }

        } else {
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
        }

        $store->setFolderContentData([
            'css' => $css,
            'js' => $js,
            'json' => $json
        ]);
    }
}