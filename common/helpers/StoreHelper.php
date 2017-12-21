<?php
namespace common\helpers;

use Yii;
use common\models\stores\Stores;
use yii\helpers\FileHelper;

/**
 * Class StoreHelper
 * @package common\helpers
 */
class StoreHelper {

    /**
     * Get assets path
     * @return bool|string
     */
    public static function getAssetsPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@webroot' . $sp . 'assets' . $sp);
    }

    /**
     * Get themes path
     * @return bool|string
     */
    public static function getThemesPath()
    {
        $sp = DIRECTORY_SEPARATOR;

        return Yii::getAlias('@app' . $sp . 'views' . $sp . 'themes' . $sp);
    }
}