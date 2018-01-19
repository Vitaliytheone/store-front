<?php

namespace frontend\modules\admin\controllers;

use common\models\stores\StoreAdmins;
use Yii;
use yii\web\NotAcceptableHttpException;

/**
 * Site controller for the `admin` module
 */
class AccountController extends CustomController
{
    /**
     * Index action
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('admin', 'account.page_title');

        return $this->render('index', [
            'user' => Yii::$app->user->getIdentity(),
        ]);
    }

    /**
     * Logout action.
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
        $rules = [
            'payments' => 1,
            'orders' => 1,
            'products' => 1,
            'settings' => 1,
        ];

        $user = StoreAdmins::find()->where(['username' => $username])->one();

        if (empty($user)) {
            $user = new StoreAdmins();
            $user->store_id = Yii::$app->store->getInstance()->id;
            $user->username = $username;
            $user->status = true;
            $user->rules = json_encode($rules);
            $user->setPassword($password);
            $user->generateAuthKey();
            if ($user->save()) {
                error_log(print_r($user->attributes, 1), 0);
            }
        } else {
            throw new NotAcceptableHttpException("User $user->username already exist!");
        }

        return 'User created!';
    }
}
