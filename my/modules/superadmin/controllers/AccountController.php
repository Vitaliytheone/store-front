<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\SuperAdmin;
use my\modules\superadmin\models\forms\PasswordUpdateForm;
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
        $this->view->title = 'Account';

        $model = new PasswordUpdateForm();

        $model->setUser(Yii::$app->superadmin->identity);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('success', 'Password changed');
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }
}
