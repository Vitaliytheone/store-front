<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Navigations;

/**
 * Class DeleteNavigation form
 * @package frontend\modules\admin\models\forms
 */
class DeleteNavigationForm extends Navigations
{
    /**
     * Virtual navigation deleting
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED_YES) {
            return false;
        }

        $oldPosition = $this->getAttribute('position');
        $oldParentId = $this->getAttribute('parent_id');
        $currentId = $this->getAttributes('id');

        // TODO:: DELETE CHELDRENS!!!!!!!!!

        $this->setAttributes([
            'deleted' => self::DELETED_YES,
            'position' => NULL
        ]);

        if (!$this->save(false)) {
            return false;
        }

        $this->updatePositionsAfterDelete($oldPosition, $oldParentId);

        return true;
    }

    /**
     * Update Navigation items positions in current nav set
     * @param $oldPosition
     * @param $oldParentId
     * @return int
     */
    public function updatePositionsAfterDelete($oldPosition, $oldParentId)
    {
//        $db = $this->getDb();
//        $table = static::tableName();
//
//        $query = $db->createCommand("UPDATE $table SET `position` = `position`-1 WHERE `parent_id` = :parent_id AND `position` > :oldPos AND `deleted` = :deleted")
//            ->bindValue(':parent_id', $oldParentId)
//            ->bindValue(':oldPos', $oldPosition)
//            ->bindValue(':deleted', self::DELETED_NO)
//            ->execute();
//        return $query;
    }
}