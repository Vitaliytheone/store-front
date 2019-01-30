<?php
namespace gateway\helpers;

use common\models\gateway\Files;
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

        $css = $js = $images = [];

        $sp = DIRECTORY_SEPARATOR;

        $assetsPath = static::getAssetsPath();

        $files = FilesHelper::getFiles();
        $assetsPath = $assetsPath . $site->folder . $sp;

        FileHelper::removeDirectory($assetsPath);
        FileHelper::createDirectory($assetsPath);

        if (!empty($files[Files::FILE_TYPE_CSS])) {
            FileHelper::createDirectory($assetsPath . 'css' . $sp);

            foreach ($files[Files::FILE_TYPE_CSS] as $file) {
                if (file_put_contents($assetsPath . 'css' . $sp . $file['name'], $file['content'])) {
                    $js[] = $file['name'];
                }
            }
        }

        if (!empty($files[Files::FILE_TYPE_JS])) {
            FileHelper::createDirectory($assetsPath . 'js' . $sp);

            foreach ($files[Files::FILE_TYPE_JS] as $file) {
                if (file_put_contents($assetsPath . 'js' . $sp . $file['name'], $file['content'])) {
                    $css[] = $file['name'];
                }
            }
        }

        if (!empty($files[Files::FILE_TYPE_IMAGE])) {
            FileHelper::createDirectory($assetsPath . 'img' . $sp);

            foreach ($files[Files::FILE_TYPE_IMAGE] as $file) {
                if (file_put_contents($assetsPath . 'img' . $sp . $file['name'], $file['content'])) {
                    $images[] = $file['name'];
                }
            }
        }

        $site->setFolderContentData([
            'css' => $css,
            'js' => $js,
            'images' => $images
        ]);
    }
}