<?php

namespace superadmin\controllers;

use control_panel\components\MainController;
use control_panel\components\SuperAccessControl;
use control_panel\helpers\Url;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Custom controller for the `superadmin` module
 */
class CustomController extends MainController
{
    public $activeTab;

    public $layout = 'superadmin.php';

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        return Controller::beforeAction($action);
    }

    /**
     * @return mixed|\yii\web\User
     */
    public function getUser()
    {
        return Yii::$app->superadmin;
    }

    /**
     * Redirect to admin panel
     * @return Response
     */
    public function goAdmin()
    {
        if (Yii::$app->superadmin->isGuest) {
            return $this->redirect(Url::toRoute('/'));
        }

        return $this->redirect(Url::toRoute('/dashboard'));
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }
}
