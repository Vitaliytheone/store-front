<?php

namespace superadmin\controllers;

use common\models\panels\SuperAdmin;
use superadmin\models\forms\PasswordUpdateForm;
use Yii;

/**
 * Account controller for the `superadmin` module
 */
class AccountController extends CustomController
{
    public $activeTab = 'account';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'account.title');

        $model = new PasswordUpdateForm();

        $model->setUser(Yii::$app->superadmin->identity);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', Yii::t('app/superadmin', 'account.password_changed'));
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }
}
