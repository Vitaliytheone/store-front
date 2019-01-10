<?php
namespace gateway\helpers;

use common\models\gateways\Sites;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class GatewayHelper
 * @package gateway\helpers
 */
class GatewayHelper {

    /**
     * Get assets path
     * @return bool|string
     */
    public static function getAssetsPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@gateway' . $sp . 'web' . $sp .'assets' . $sp);
    }

    /**
     * Get themes path
     * @return bool|string
     */
    public static function getThemesPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@gateway' . $sp . 'views' . $sp . 'themes' . $sp);
    }

    /**
     * Generate themes assets
     * @param int $id
     * @return bool
     */
    public static function generateAssets($id)
    {
        if (!($site = Sites::findOne($id))) {
            return false;
        }

        $css = $customStyles = $standardStyles = [];
        $js = $customScripts = $standardScripts = [];
        $json = $customJson = $standardJson = [];


        $sp = DIRECTORY_SEPARATOR;

        $assetsPath = static::getAssetsPath();
        $themesPath = static::getThemesPath();

        $themePath = $site->theme_folder;

        $customThemeFiles = $site->getThemeFiles();
        $standardThemePath = $themesPath . 'default' . $sp . $themePath . $sp;
        $assetsPath = $assetsPath . $site->folder . $sp;

        FileHelper::removeDirectory($assetsPath);
        FileHelper::createDirectory($assetsPath);

        if (!empty($customThemeFiles)) {
            foreach ($customThemeFiles as $customThemeFile) {
                if (preg_match("/\.js$/uis", $customThemeFile['name'])) {
                    $customScripts[$customThemeFile['name']] = $customThemeFile['content'];
                }

                if (preg_match("/\.css$/uis", $customThemeFile['name'])) {
                    $customStyles[$customThemeFile['name']] = $customThemeFile['content'];
                }
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

        foreach ($standardStyles as $fileName => $filePath) {
            if (isset($customStyles[$fileName])) {
                if (file_put_contents($assetsPath . 'css' . $sp . $fileName, $customStyles[$fileName])) {
                    $css[] = $fileName;
                }
                continue;
            }

            if (file_put_contents($assetsPath . 'css' . $sp . $fileName, file_get_contents($filePath))) {
                $css[] = $fileName;
            }
        }

        foreach ($standardScripts as $fileName => $filePath) {
            if (isset($customScripts[$fileName])) {
                if (file_put_contents($assetsPath . 'js' . $sp . $fileName, $customScripts[$fileName])) {
                    $js[] = $fileName;
                }
                continue;
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

        $site->setFolderContentData([
            'css' => $css,
            'js' => $js,
            'json' => $json
        ]);
    }
}