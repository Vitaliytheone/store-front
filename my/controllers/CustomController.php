<?php

namespace my\controllers;

use my\components\MainController;
use my\helpers\UserHelper;
use my\models\Auth;
use Yii;

/**
 * Custom controller
 */
class CustomController extends MainController
{
    /**
     * @return mixed|\yii\web\User
     */
    public function getUser()
    {
        return Yii::$app->user;
    }

    public function init()
    {
        if (!Yii::$app->user->isGuest) {
            /**
             * @var $user Auth
             */
            $user = Yii::$app->user->identity;
            $hash = UserHelper::getHash();

            if (!$user->validateAuthKey($hash) || $user->status != 1) {
                Yii::$app->user->logout();
            }
        }
    }

    /**
     * Custom before action
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest || ('site' == $action->controller->id && in_array($action->id, [
                'restore',
                'signup',
                'invoice',
                'checkout',
                'signin',
            ]))) {
            $this->layout = 'guest';
        }

        return parent::beforeAction($action);
    }
}