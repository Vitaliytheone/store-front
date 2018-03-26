<?php

namespace my\modules\superadmin\controllers;

use my\components\MainController;
use my\components\SuperAccessControl;
use my\helpers\Url;
use Yii;
use yii\web\Response;

/**
 * Custom controller for the `superadmin` module
 */
class CustomController extends MainController
{
    public $activeTab;
    public $layout = '@my/modules/superadmin/views/layouts/superadmin.php';

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

        return $this->redirect(Url::toRoute('/panels'));
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::className(),
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
