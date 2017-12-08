<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Packages;

class MovePackageForm extends Packages
{
    /**
     * Move package to new position
     * @param $newPosition
     * @return bool|int
     */
    public function changePosition($newPosition)
    {
        $maxPosition = $this->getMaxPosition();
        $currentPosition = $this->getAttribute('position');
        $productId = $this->getAttribute('product_id');

        if ($newPosition < 0 || $newPosition > $maxPosition) {
            return false;
        }

        $db = $this->getDb();
        $packagesTable = static::tableName();

        $query = $db->createCommand("
                  UPDATE $packagesTable SET
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
                  WHERE `deleted` = :deleted AND `product_id` = :product
            ")
            ->bindValue(':newPos', $newPosition)
            ->bindValue(':curPos', $currentPosition)
            ->bindValue(':deleted', self::DELETED_NO)
            ->bindValue(':product', $productId)
            ->execute();

        if ($query) {
            $this->setAttribute('position', $newPosition);
        }

        return $this->getAttribute('position');
    }
}