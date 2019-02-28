<?php

namespace superadmin\controllers;

use control_panel\components\SuperAccessControl;
use control_panel\helpers\Url;
use superadmin\models\forms\LoginForm;
use Yii;
use yii\filters\VerbFilter;

/**
 * Site controller for the `superadmin` module
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
                'class' => SuperAccessControl::class,
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
            /*'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],*/
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

        if (!Yii::$app->superadmin->isGuest) {
            return $this->goAdmin();
        }

        $this->layout = 'guest';

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $redirect = Yii::$app->request->get('r', Url::toRoute('/'));
            return $this->redirect($redirect);
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->superadmin->logout(false);

        return $this->goAdmin();
    }
}