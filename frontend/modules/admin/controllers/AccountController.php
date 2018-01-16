<?php

namespace frontend\modules\admin\controllers;

use common\models\stores\StoreAdmins;
use frontend\modules\admin\components\Url;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Site controller for the `admin` module
 */
class AccountController extends CustomController
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
     * Renders account management page
     * @return string
     */
    public function actionIndex()
    {

    }

    // TODO:: REMOVE THIS ACTION!!!
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
