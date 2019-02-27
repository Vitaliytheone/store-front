<?php
namespace store\helpers;

use common\models\store\Blocks;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class BlockHelper
 * @package store\helpers
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

        $path = Yii::getAlias('@store/web/js/blocks.json');

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
     * Get block by code or create new if not exist
     * @param string $code
     * @param bool $createOnEmpty
     * @return null|Blocks
     * @throws NotFoundHttpException
     */
    public static function getBlock($code, $createOnEmpty = false)
    {
        $block = null;

        if (!($block = Blocks::findOne(['code' => $code]))) {

            if (!$createOnEmpty || !in_array($code, Blocks::getCodes())) {
                return null;
            }

            $block = new Blocks();
            $block->code = $code;
            $block->setContent(BlockHelper::getDefaultBlock($block->code));
            $block->save(false);
        }

        return $block;
    }

    /**
     * Get blocks content as array
     * 1. Return all available blocks. Return default block if store block not exist.
     * 2. Check store block for compatible default settings. Missed field will be added.
     * @return array
     */
    public static function getBlocksContent()
    {
        $defaultBlocks = static::getDefaultBlocks();

        $storeBlocks = Blocks::find()
            ->indexBy('code')
            ->all();

        $blocks = [];

        foreach ($defaultBlocks as $code => $defaultContent) {
            $storeBlock = ArrayHelper::getValue($storeBlocks, $code, null);

            if ($storeBlock instanceof Blocks) {
                $blockContent = $storeBlock->getContent();
                self::checkBlockContent($blockContent, $defaultContent);
                $blocks[$code] = $blockContent;
            } else {
                $blocks[$code] = $defaultContent;
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
    public static function checkBlockContent(&$block, $defaultBlock)
    {
        static $checkingPath = [];
        static $defaultDataItem = null;

        foreach ($defaultBlock as $defaultKey => $defaultValue) {

            if (is_array($defaultValue)) {
                $checkingPath[] = $defaultKey;
                static::checkBlockContent($block, $defaultValue);
            } else {

                $checkingKey = implode('.', $checkingPath) . ($checkingPath ? '.' : '') . $defaultKey;

                // Check all fields-values except `data` array
                if (!in_array('data', explode('.', $checkingKey))) {

                    // Restore only missed fields
                    $keyExist = ('#no_key#' !== ArrayHelper::getValue($block, $checkingKey, '#no_key#'));

                    if (!$keyExist) {
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
                            // Restore only missed fields
                            $keyExist = ('#no_key#' !== ArrayHelper::getValue($item, $defaultDataKey, '#no_key#'));

                            if (!$keyExist) {
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