<?php

namespace sommerce\models\search;

use common\models\stores\Stores;
use sommerce\helpers\BlockHelper;
use common\models\store\Blocks;

/**
 * Class BlocksSearch
 * @package sommerce\models\search
 */
class BlocksSearch
{
    /**
     * @param Stores $store
     * @return array
     */
    public static function search($store)
    {
        $blocks = [];
        foreach (Blocks::find()->all() as $block) {
            if ($store->isEnableBlock($block->code)) {
                $blocks[$block->code] = $block->getContent(BlockHelper::getDefaultBlock($block->code));
            }
        }
        return $blocks;
    }
    
}