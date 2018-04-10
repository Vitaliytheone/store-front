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
    /** @inheritdoc */
    protected function beforeLogin($identity, $cookieBased, $duration)
    {

//        StoreAdminsHash::updateFreshness($identity->id);

        return parent::beforeLogin($identity, $cookieBased, $duration); // TODO: Change the autogenerated stub
    }

    /** @inheritdoc */
    protected function afterLogout($identity)
    {
//        /** @var StoreAdminAuth $identity */
//        $identity->deleteAuthKey();

        parent::afterLogout($identity); // TODO: Change the autogenerated stub
    }

}