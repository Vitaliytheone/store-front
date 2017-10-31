<?php

namespace frontend\controllers;

use common\components\MainController;
use yii\filters\AccessControl;

/**
 * Custom controller for the `admin` module
 */
class CustomController extends MainController
{
    public $layout = '@frontend/views/site/layout.php';

    /**
     * @inheritdoc
     */
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ]
                ],
            ],
        ];
    }*/

    public function beforeAction($action)
    {
        $this->addModule('frontendLayout');

        return parent::beforeAction($action);
    }
}
