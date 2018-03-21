<?php

namespace sommerce\modules\admin\models\search;

use common\models\stores\Stores;
use Yii;

/**
 * Class BlocksSearch
 * @package app\models\search
 */
class BlocksSearch
{
    /**
     * @var Stores
     */
    private $_store;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Get blocks
     * @return array
     */
    public function getBlocks()
    {
        $blocks = [];

        foreach ($this->_store->getBlocks() as $block => $label) {
            $blocks[] = [
                'code' => $block,
                'label' => $label,
                'active' => $this->_store->isEnableBlock($block)
            ];
        }

        return $blocks;
    }
}