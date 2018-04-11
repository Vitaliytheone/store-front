<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreAdminsHash;
use Yii;
use yii\base\Model;

/**
 * Class SuperLoginForm
 * @package sommerce\modules\admin\models\forms
 */
class SuperLoginForm extends Model
{
    /**
     * Logs in a user using the provided username and password.
     * @param $token string
     * @return bool whether the user is logged in successfully
     */
    public function login(string $token)
    {
        $user = StoreAdminAuth::findByToken($token);

        if (!$user) {
            return false;
        }

        $hash = $user->generateAuthKey();
        StoreAdminsHash::setHash($user->id, $hash, StoreAdminsHash::MODE_SUPERADMIN_ON);

        Yii::$app->session->set(StoreAdminAuth::SESSION_KEY_ADMIN_HASH, $hash);

        if (!Yii::$app->user->login($user)) {
            return false;
        }

        ActivityLog::log($user, ActivityLog::E_SUPERADMIN_AUTHORIZATION_BY_TOKEN);

        return true;
    }
}
