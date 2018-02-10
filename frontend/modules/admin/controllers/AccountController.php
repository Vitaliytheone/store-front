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
}
