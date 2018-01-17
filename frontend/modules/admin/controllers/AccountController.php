<?php

namespace frontend\modules\admin\controllers;

use common\models\stores\StoreAdmins;
use Yii;

/**
 * Site controller for the `admin` module
 */
class AccountController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        $user = Yii::$app->user;
        $user->logout();
        $user->loginRequired();
    }

    /**
     *   -----   TODO:: REMOVE THIS ACTION for production !!!
     */
    public function actionAddAdmin($username, $password)
    {
        $user = StoreAdmins::find()->where(['username' => $username])->one();
        if (empty($user)) {
            $user = new StoreAdmins();
            $user->store_id = Yii::$app->store->getInstance()->id;
            $user->username = $username;
            $user->status = true;
            $user->setPassword($password);
            $user->generateAuthKey();
            if ($user->save()) {
                error_log(print_r($user->attributes, 1), 0);
            }
        }
    }
}
