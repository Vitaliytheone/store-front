<?php

namespace admin\models\forms;


use common\models\gateways\Admins;
use common\models\gateways\AdminsHash;
use yii\base\Model;
use Yii;

/**
 * Class SuperLoginFom
 * @package admin\models\forms
 */
class SuperLoginFom extends Model
{
    /**
     * @param string $token
     * @return bool
     * @throws \Exception
     */
    public function login(string $token)
    {
        $user = Admins::findByToken($token);

        if (!$user) {
            return false;
        }

        $hash = $user::generateAuthKey($user->getId());

        AdminsHash::deleteByHash($hash);
        AdminsHash::setHash($user->id, $hash, AdminsHash::MODE_SUPERADMIN_ON);

        if (!Yii::$app->user->login($user)) {
            return false;
        }

        return true;
    }
}