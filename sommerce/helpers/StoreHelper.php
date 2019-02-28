<?php

namespace sommerce\helpers;

use Yii;

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

}