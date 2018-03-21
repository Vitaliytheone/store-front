<?php
namespace sommerce\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class BlockHelper
 * @package sommerce\helpers
 */
class BlockHelper {

    /**
     * @var array
     */
    static $blocks = [];

    /**
     * Get default blocks
     * @return array
     */
    public static function getDefaultBlocks()
    {
        if (!empty(static::$blocks)) {
            return static::$blocks;
        }

        $path = Yii::getAlias('@sommerce/web/js/blocks.json');

        if (!file_exists($path)) {
            return static::$blocks;
        }

        $blocks = file_get_contents($path);
        $blocks = @json_decode($blocks, true);

        if (is_array($blocks)) {
            static::$blocks = $blocks;
        }

        return static::$blocks;
    }

    /**
     * Get default block by code
     * @param string $code
     * @return array
     */
    public static function getDefaultBlock($code)
    {
        return ArrayHelper::getValue(static::getDefaultBlocks(), $code, []);
    }
}