<?php

namespace sommerce\helpers;

use Yii;
use common\models\stores\Stores;
use yii\helpers\FileHelper;

/**
 * Class StoreHelper
 * @package sommerce\helpers
 */
class StoreHelper
{

    /**
     * Get assets path
     * @return bool|string
     */
    public static function getAssetsPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@sommerce' . $sp . 'web' . $sp . 'assets' . $sp);
    }


    /**
     * Generate themes assets
     * @param int $id
     * @return bool
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
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


        $assetsPath = $assetsPath . $store->folder . $sp;

        FileHelper::removeDirectory($assetsPath);
        FileHelper::createDirectory($assetsPath);


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