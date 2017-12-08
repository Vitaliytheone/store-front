<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Products;

/**
 * Class MoveProductForm
 * @package frontend\modules\admin\models\forms
 */
class MoveProductForm extends Products
{
    /**
     * Move product to new position
     * @param $newPosition
     * @return bool|int
     */
    public function changePosition($newPosition)
    {
        $maxPosition = static::getMaxPosition();
        $currentPosition = $this->getAttribute('position');

        if ($newPosition < 0 || $newPosition > $maxPosition) {
            return false;
        }

        $db = $this->getDb();
        $table = static::tableName();

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
            $this->setAttribute('position', $newPosition);
        }

        return $this->getAttribute('position');
    }
}