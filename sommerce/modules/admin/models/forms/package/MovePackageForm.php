<?php

namespace  admin\models\forms\package;

use admin\models\forms\BaseForm;
use common\models\store\ActivityLog;
use common\models\store\Packages;

/**
 * Class MovePackageForm
 * @package admin\models\forms\package
 */
class MovePackageForm extends BaseForm
{
    /**
     * @var Packages
     */
    protected $_package;

    /**
     * @param Packages $package
     */
    public function setProduct(Packages $package)
    {
        $this->_package = $package;
    }

    /**
     * Move package to new position
     * @param $newPosition
     * @return bool|int
     */
    public function changePosition($newPosition)
    {
        $maxPosition = $this->_package->getMaxPosition();
        $currentPosition = $this->_package->getAttribute('position');
        $productId = $this->_package->getAttribute('product_id');

        if ($newPosition < 0 || $newPosition > $maxPosition) {
            return false;
        }

        $db = $this->_package->getDb();
        $packagesTable = Packages::tableName();

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
            ->bindValue(':deleted', Packages::DELETED_NO)
            ->bindValue(':product', $productId)
            ->execute();

        if ($query) {
            $this->_package->setAttribute('position', $newPosition);
        }

        ActivityLog::log($this->_user, ActivityLog::E_PACKAGES_PACKAGE_POSITION_CHANGED, $this->_package->id, $this->_package->id);

        return $this->_package->getAttribute('position');
    }
}