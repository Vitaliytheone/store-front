<?php

namespace frontend\modules\admin\controllers;

use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Site controller for the `admin` module
 */
class SiteController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'log_in';
        $this->view->title = Yii::t('admin', 'login.sign_in_page_title');

        $form = new LoginForm();

        if (
            $form->load(Yii::$app->getRequest()->post()) &&
            $form->validate() &&
            $form->login()
        ) {
            error_log(Yii::$app->user->getIsGuest() ? 'Guest!' : 'Sign in OK!');

            $this->redirect(Url::toRoute('/orders'));
        }

        return $this->render('sign_in', [
            'form' => $form,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
    }
}
