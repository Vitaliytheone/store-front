<?php

namespace gateway\modules\admin\controllers;

use common\models\gateways\Admins;
use gateway\helpers\UiHelper;
use gateway\modules\admin\models\forms\AccountForm;
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

        /**
         * @var Admins $user
         */
        $user = Yii::$app->user->getIdentity();
        $form = new AccountForm();
        $form->setUser($user);

        if ($form->load(Yii::$app->getRequest()->post()) && $form->changePassword()) {
            UiHelper::message(Yii::t('admin', 'account.message_password_changed'));
        }

        return $this->render('index', [
            'form' => $form,
        ]);
    }
}
