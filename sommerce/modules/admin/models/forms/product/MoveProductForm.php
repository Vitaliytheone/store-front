<?php

namespace admin\models\forms\product;

use admin\models\forms\BaseForm;
use common\models\sommerce\ActivityLog;
use common\models\sommerce\Products;

/**
 * Class MoveProductForm
 * @package admin\models\forms\product
 */
class MoveProductForm extends BaseForm
{
    /**
     * @var Products
     */
    protected $_product;

    /**
     * @param Products $product
     */
    public function setProduct(Products $product)
    {
        $this->_product = $product;
    }

    /**
     * Move product to new position
     * @param $newPosition
     * @return bool|int
     */
    public function changePosition($newPosition)
    {
        $maxPosition = Products::getMaxPosition();
        $currentPosition = $this->_product->getAttribute('position');

        if ($newPosition < 0 || $newPosition > $maxPosition) {
            return false;
        }

        $db = $this->_product->getDb();
        $table = Products::tableName();

        $query = $db->createCommand("
                  UPDATE $table SET
                      `position` = CASE
                          WHEN (`position` = :curPos) THEN 
                                :newPos                       -- replace new within old
                          WHEN (`position` > :curPos and `position` <= :newPos) THEN 
                                `position`- 1                 -- moving up
                          WHEN (`position` < :curPos and `position` >= :newPos) THEN 
                                `position`+ 1                 -- moving down
                          ELSE 
                                `position`                    -- otherwise lets keep same value.
                      END
            ")
            ->bindValue(':newPos', $newPosition)
            ->bindValue(':curPos', $currentPosition)
            ->execute();

        if ($query) {
            $this->_product->setAttribute('position', $newPosition);
        }

        ActivityLog::log($this->_user, ActivityLog::E_PRODUCTS_PRODUCT_POSITION_CHANGED, $this->_product->id, $this->_product->id);

        return $this->_product->getAttribute('position');
    }
}