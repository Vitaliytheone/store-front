<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use yii\web\User;
use common\models\store\Packages;

class MovePackageForm extends Packages
{
    /**
     * @var User
     */
    protected $_user;

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }


    /**
     * Move product to new position
     * @param array $postData
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function changePosition(array $postData): bool
    {
        if (empty($postData) || !isset($postData['id']) || !isset($postData['list'])) {
            $this->addError('Change position: bad data');
            return false;
        }

        if ($this->id !== $postData['id']) {
            $this->addError('Change position: bad data (incorrect id)');
            return false;
        }

        $db = $this->getDb();
        $table = static::tableName();

        $sqlQuery = "UPDATE $table SET
                      `position` = CASE";

        foreach ($postData['list'] as $element) {
            if (!isset($element['id']) || !isset($element['position'])) {
                $this->addError('Change position: bad data (incorrect list)');
                return false;
            }

            $sqlQuery .= "WHEN (`id` = " . $element['id'] . ") THEN " . $element['position'];
        }

        $sqlQuery .= "ELSE `position`
                      END";
        $query = $db->createCommand($sqlQuery)->execute();

        if (!$query) {
            $this->addError('Changing position error');
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_POSITION_CHANGED, $this->id, $this->id);

        return true;
    }
}