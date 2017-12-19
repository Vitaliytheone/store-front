<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Navigations;
use frontend\models\search\NavigationsSearch;

/**
 * Class DeleteNavigation form
 * @package frontend\modules\admin\models\forms
 */
class DeleteNavigationForm extends Navigations
{
    /**
     * Virtual navigation deleting
     * Deleting item and all subitems
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED_YES) {
            return false;
        }

        // Delete self
        $id = $this->getAttribute('id');
        $parentPosition = $this->getAttribute('position');
        $parentId = $this->getAttribute('parent_id');

        $this->setAttributes([
            'deleted' => self::DELETED_YES,
            'position' => null,
        ]);
        $this->save();

        $idsToLevelUp = NavigationsSearch::getFirstLevelChildrenIds($id);
        $countLevelUpItems = count($idsToLevelUp);

        $table = static::tableName();

        // Level up positions of exiting items with same level
        $this->getDb()->createCommand("
            UPDATE $table
            SET `position` = `position` + :numPositionUp
            WHERE `parent_id` = :deletedId AND `position` > :deletedPosition AND `deleted` = :deleted
        ")
            ->bindValue(':deletedId', $parentId)
            ->bindValue(':deletedPosition', $parentPosition)
            ->bindValue(':numPositionUp', $countLevelUpItems - 1)
            ->bindValue(':deleted', self::DELETED_NO)
            ->execute();

        $idsToLevelUpImploded = '(' . implode(',', $idsToLevelUp) . ')';

        // Level up level _all_ children of deleted node
        $this->getDb()->createCommand("    
          SET @tempVariable:= :position;     
          UPDATE $table
          SET `parent_id` = :parentId, `position` = (@tempVariable := @tempVariable + 1) 
          WHERE `id` IN $idsToLevelUpImploded;
        ")
            ->bindValue(':parentId', $parentId)
            ->bindValue(':position', $parentPosition - 1)
            ->execute();

        return true;
    }

}