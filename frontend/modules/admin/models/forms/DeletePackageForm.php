<?php

namespace frontend\modules\admin\models\forms;

use  common\models\store\Packages;

/**
 * Class DeletePackageForm
 * @package frontend\modules\admin\models\forms
 */
class DeletePackageForm extends Packages
{
    /**
     * Virtual package deleting
     * @return bool
     */
    public function deleteVirtual()
    {
        if ($this->deleted == self::DELETED) {
            return false;
        }

        $oldPosition = $this->getAttribute('position');

        $this->setAttributes([
            'deleted' => self::DELETED,
            'position' => NULL
        ]);
        if (!$this->save(false)) {
            return false;
        }
        $this->updatePositionsAfterDelete($oldPosition);
        return true;
    }

    /**
     * Update packages positions in current product set
     * @param $oldPosition
     * @return int
     */
    public function updatePositionsAfterDelete($oldPosition)
    {
        $db = $this->getDb();
        $table = static::tableName();

        $productId = $this->getAttribute('product_id');
        $query = $db->createCommand("UPDATE $table SET `position` = `position`-1 WHERE `product_id` = :product AND `position` > :oldPos AND `deleted` = :deleted")
            ->bindValue(':product', $productId)
            ->bindValue(':oldPos', $oldPosition)
            ->bindValue(':deleted', self::DELETED_NO)
            ->execute();
        return $query;
    }
}