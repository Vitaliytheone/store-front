<?php

namespace store\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\Products;
use common\models\stores\StoreAdminAuth;
use yii\web\User;

/**
 * Class MoveProductForm
 * @package store\modules\admin\models\forms
 */
class MoveProductForm extends Products
{
    /**
     * @var User
     */
    protected $_user;

    /**
     * Set current user
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Get current user
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

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

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PRODUCTS_PRODUCT_POSITION_CHANGED, $this->id, $this->id);

        return $this->getAttribute('position');
    }
}