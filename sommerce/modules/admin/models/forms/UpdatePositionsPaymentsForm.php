<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StorePaymentMethods;
use yii\web\User;
use yii\helpers\ArrayHelper;

/**
 * Class UpdatePositionsPaymentsForm
 * @package sommerce\modules\admin\models\forms
 */
class UpdatePositionsPaymentsForm extends StorePaymentMethods
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
     * Move payment method to new position
     * @param $postData array
     * @return bool
     * @throws \Throwable
     */
    public function updatePositions($postData): bool
    {

        $positionsTree = ArrayHelper::getValue($postData, 'positions');

        if (!$positionsTree) {
            return false;
        }

        $positions = static::flatten($positionsTree);

        if (empty($positions)) {
            return false;
        }

        $positionsImploded = null;
        foreach ($positions as $position) {
            $id = (int)$position['id'] | 0;
            $idsFront[] = (int)$position['id'] | 0;
            $position = (int)$position['position'] | 0;
            $positionsImploded = ($positionsImploded ? $positionsImploded . ',' : '') . "('$id', '$position')";
        }

        $db = static::getDb();
        $table = static::tableName();

        $positionList = static::find()->where(['id' => $idsFront])->select('id')->indexBy('id')->asArray()->all();

        if (empty($positionList)) {
            return false;
        }

        $idsDb = array_column($positionList, 'id');
        \Yii::debug($positionsImploded);
        \Yii::debug($positionList);
        \Yii::debug($idsDb);
        \Yii::debug($idsFront);
        if (!in_array($idsFront, $idsDb)) {
            return false;
        }

        $command = $db->createCommand("
                INSERT INTO $table (id, position)
                VALUES $positionsImploded
                ON DUPLICATE KEY UPDATE position=VALUES(position)
        ")->execute();

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAYMENTS_SPM_ITEM_POSITION_CHANGED, $this->id, $this->id);

        return true;
    }

    /**
     * Flatten position tree array
     * @param $tree array
     * @return array
     */
    private static function flatten($tree): array
    {
        $flatArray = [];

        foreach ($tree as $position => $node) {
            $flatArray[] = [
                'id' => $node['id'],
                'position' => $position,
            ];
        }

        return $flatArray;
    }

}