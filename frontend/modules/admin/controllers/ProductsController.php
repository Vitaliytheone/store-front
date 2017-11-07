<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use frontend\helpers\Ui;
use frontend\modules\admin\forms\ProductForm;

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

    /**
     * Create new Product AJAX action
     * @return array
     * @throws yii\web\NotAcceptableHttpException
     */
    public function actionCreateProduct()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }

        $formData = $request->post();
        $productModel = new ProductForm();
        if (!$productModel->load($formData)) {
            throw new yii\web\NotAcceptableHttpException();
        }
        if (!$productModel->validate()) {
            return $response->data = ['error' => [
                'message' => 'Model validation error',
                'html' => Ui::errorSummary($productModel, ['class' => 'alert-danger alert']),
            ]];
        }
        if (!$productModel->save()) {
            throw new yii\web\NotAcceptableHttpException();
        }

        return $response->data = [
            'product' => $productModel,
        ];
    }

}