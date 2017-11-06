<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\store\Products;

/**
 * Class ProductsController
 * @package frontend\modules\admin\controllers
 */
class ProductsController extends CustomController
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

    public function beforeAction($action)
    {
        // Add custom JS modules
        $this->addModule('ordersDetails');
        return parent::beforeAction($action);
    }

    /**
     * Render found products-packages list
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }

}