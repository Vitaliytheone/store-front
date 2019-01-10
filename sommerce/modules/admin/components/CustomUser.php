<?php
namespace sommerce\modules\admin\components;

use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreAdminsHash;
use Yii;
use yii\web\User;

/**
 * Class CustomUser
 * @package sommerce\modules\admin\components
 */
class CustomUser extends User
{
    /**
     * @param StoreAdminAuth $identity
     * @param bool $cookieBased
     * @param int $duration
     * @return bool
     */
    protected function beforeLogin($identity, $cookieBased, $duration)
    {
        return parent::beforeLogin($identity, $cookieBased, $duration);
    }

    /**
     * @param StoreAdminAuth $identity
     * @param bool $cookieBased
     * @param int $duration
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        parent::afterLogin($identity, $cookieBased, $duration);

        StoreAdminsHash::updateFreshnessCurrentAdmin();

        if ($identity->isSuperAdmin()) {
            StoreAdminsHash::deleteOld(StoreAdminsHash::MODE_SUPERADMIN_ON, 30 * 60);
        }
    }

    /**
     * @param StoreAdminAuth $identity
     */
    protected function afterLogout($identity)
    {
        parent::afterLogout($identity);

        StoreAdminsHash::deleteOld(StoreAdminsHash::MODE_SUPERADMIN_ON, 30 * 60);

        if (!$identity->isSuperAdmin()) {
            StoreAdminsHash::deleteByUser($identity->getId());
        }
    }
}