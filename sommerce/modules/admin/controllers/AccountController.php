<?php

namespace sommerce\modules\admin\controllers;

use sommerce\helpers\UiHelper;
use sommerce\modules\admin\models\forms\AccountForm;
use Yii;

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

        $form = new AccountForm();
        $form->setUser(Yii::$app->user);

        if ($form->load(Yii::$app->getRequest()->post()) && $form->changePassword()) {
            UiHelper::message(Yii::t('admin', 'account.message_password_changed'));
        }

        return $this->render('index', [
            'form' => $form,
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
