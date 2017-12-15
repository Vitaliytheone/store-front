<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Navigations;
use frontend\modules\admin\models\search\NavigationsSearch;

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

        $id = $this->getAttribute('id');
        $position = $this->getAttribute('position');
        $parentId = $this->getAttribute('parent_id');

        $idsToDelete = NavigationsSearch::getChildrenTreeNodeIds($id);
        array_push($idsToDelete, $id);

        $idsToDeleteImploded = '(' . implode(',', $idsToDelete) . ')';

        // `Delete` item and all subitems
        $table = static::tableName();
        $query = $this->getDb()->createCommand("UPDATE $table SET `position` = :position, `deleted` = :deleted WHERE `id` IN $idsToDeleteImploded")
            ->bindValue(':position', null)
            ->bindValue(':deleted', self::DELETED_YES)
            ->execute();

        $this->updatePositionsAfterDelete($position, $parentId);

        return $query;
    }

    /**
     * Update Navigation items positions in current nav set
     * @param int $position Position of deleted item
     * @param int $parentId Parent ID of deleted item
     * @return int
     */
    public function updatePositionsAfterDelete($position, $parentId)
    {
        $table = static::tableName();
        $query = $this->getDb()->createCommand("UPDATE $table SET `position` = `position`-1 WHERE `parent_id` = :parentId AND `position` > :oldPosition AND `deleted` = :deleted")
            ->bindValue(':parentId', $parentId)
            ->bindValue(':oldPosition', $position)
            ->bindValue(':deleted', self::DELETED_NO)
            ->execute();

        return $query;
    }
}