<?php

namespace frontend\modules\admin\controllers;

use frontend\modules\admin\models\search\LevopanelsSearch;
use Yii;
use yii\filters\AccessControl;

/**
 * Class LevopanelController
 * @package frontend\modules\admin\controllers
 */
class LevopanelController extends CustomController
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
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->getRequest();
        $this->view->title = 'Levopanel data';

        $search = new LevopanelsSearch();

        return $this->render('index', [
            'panels' => $search->searchPanels(),
            'statusButtons' => $search->getStatusButtons(),
        ]);
    }
}
