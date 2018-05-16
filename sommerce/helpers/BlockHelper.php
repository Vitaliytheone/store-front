<?php
namespace sommerce\helpers;

use common\models\store\Blocks;
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

    /**
     * Get blocks settings as array
     * 1. Return all available blocks. Return default block if store block not exist.
     * 2. Check store block for compatible default settings. Missed field will be added.
     * @return array
     */
    public static function getBlocks()
    {
        $defaultBlocks = static::getDefaultBlocks();

        $storeBlocks = Blocks::find()
            ->indexBy('code')
            ->all();

        $blocks = [];

        foreach ($defaultBlocks as $code => $defaultBlock) {
            $storeBlock = ArrayHelper::getValue($storeBlocks, $code, null);

            if ($storeBlock instanceof Blocks) {
                $block = $storeBlock->getContent();
                self::checkBlockDefaults($block, $defaultBlock);
                $blocks[$code] = $block;
            } else {
                $blocks[$code] = $defaultBlock;
            }
        }

        return $blocks;
    }

    /**
     * Check if block has all defaultBlock fields/values.
     * Return checked block with missed default fields/values.
     * @param $block
     * @param $defaultBlock
     * @return array
     */
    public static function checkBlockDefaults(&$block, $defaultBlock)
    {
        static $checkingPath = [];
        static $defaultDataItem = null;

        foreach ($defaultBlock as $defaultKey => $defaultValue) {

            if (is_array($defaultValue)) {
                $checkingPath[] = $defaultKey;
                static::checkBlockDefaults($block, $defaultValue);
            } else {

                $checkingKey = implode('.', $checkingPath) . ($checkingPath ? '.' : '') . $defaultKey;

                // Check all fields-values except `data` array
                if (!in_array('data', explode('.', $checkingKey))) {
                    if (!ArrayHelper::getValue($block, $checkingKey)) {
                        ArrayHelper::setValue($block, $checkingKey, $defaultValue);
                    }
                } else {
                    // Check each 'data' items defaults
                    $defaultDataKey = explode('.', $checkingKey);
                    array_shift($defaultDataKey);
                    $defaultDataItemIndex = array_shift($defaultDataKey);
                    $defaultDataKey = implode('.', $defaultDataKey);

                    // Use default item with index = 0
                    if ($defaultDataItemIndex == 0) {

                        $blockDataItems = ArrayHelper::getValue($block, 'data', []);

                        foreach ($blockDataItems as &$item) {
                            if (!ArrayHelper::getValue($item, $defaultDataKey)) {
                                ArrayHelper::setValue($item, $defaultDataKey, $defaultValue);
                            }
                        }

                        $block['data'] = $blockDataItems;
                    }
                }
            }
        }

        array_pop($checkingPath);
    }
}