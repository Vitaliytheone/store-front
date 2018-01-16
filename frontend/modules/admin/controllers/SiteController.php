<?php

namespace frontend\modules\admin\controllers;

use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use common\components\MainController;

/**
 * Site controller for the `admin` module
 */
class SiteController extends MainController
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
        $this->layout = 'login';
        $this->view->title = Yii::t('admin', 'login.sign_in_page_title');

        $form = new LoginForm();

        if (
            $form->load(Yii::$app->getRequest()->post()) &&
            $form->validate() &&
            $form->login()
        ) {
            $this->redirect(Url::toRoute('/orders'));
        }

        return $this->render('signin', [
            'form' => $form,
        ]);
    }
}
