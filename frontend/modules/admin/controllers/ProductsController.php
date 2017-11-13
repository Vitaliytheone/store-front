<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use frontend\helpers\Ui;
use frontend\modules\admin\forms\ProductForm;
use frontend\modules\admin\forms\PackageForm;

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
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }
        $productModel = new ProductForm();
        $postData = $productModel->checkPropertiesField($request->post());
        if (!$productModel->load($postData)) {
            throw new NotAcceptableHttpException();
        }
        if (!$productModel->validate()) {
            return $response->data = ['error' => [
                'message' => 'Model validation error',
                'html' => Ui::errorSummary($productModel, ['class' => 'alert-danger alert']),
            ]];
        }
        if (!$productModel->save()) {
            throw new NotAcceptableHttpException();
        }
        return [
            'product' => $productModel,
        ];
    }

    /**
     * Get Product AJAX action
     * @param $id
     * @return array
     * @throws Yii\web\NotFoundHttpException
     */
    public function actionGetProduct($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }

        $productModel = ProductForm::findOne($id);
        if (!$productModel) {
            throw new NotFoundHttpException();
        }
        return [
            'product' => $productModel->getAttributes(),
        ];
    }

    /**
     * Update Product AJAX action
     * @param $id
     * @return array
     * @throws Yii\web\NotAcceptableHttpException
     * @throws Yii\web\NotFoundHttpException
     */
    public function actionUpdateProduct($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }
        $productModel = ProductForm::findOne($id);
        if (!$productModel) {
            throw new NotFoundHttpException();
        }
        $postData = $productModel->checkPropertiesField($request->post());
        if (!$productModel->load($postData)) {
            throw new NotAcceptableHttpException();
        }
        if (!$productModel->validate()) {
            return ['error' => [
                'message' => 'Model validation error',
                'html' => Ui::errorSummary($productModel, ['class' => 'alert-danger alert']),
            ]];
        }
        if (!$productModel->save(false)) {
            throw new NotAcceptableHttpException();
        }
        return [
            'product' => $productModel,
        ];
    }

    /**
     * Create new Package AJAX action
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionCreatePackage()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }

        $packageModel = new PackageForm();
        if (!$packageModel->load($request->post())) {
            throw new NotAcceptableHttpException();
        }
        if (!$packageModel->validate()) {
            return $response->data = ['error' => [
                'message' => 'Model validation error',
                'html' => Ui::errorSummary($packageModel, ['class' => 'alert-danger alert']),
            ]];
        }
        if (!$packageModel->save()) {
            throw new NotAcceptableHttpException();
        }
        return [
            'package' => $packageModel,
        ];
    }

    /**
     * Get Package AJAX action
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetPackage($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }

        $packageModel = PackageForm::findOne($id);
        if (!$packageModel) {
            throw new NotFoundHttpException();
        }
        return [
            'package' => $packageModel->getAttributes(),
        ];
    }

    /**
     * Update Package AJAX action
     * @param $id
     * @return array
     * @throws Yii\web\NotAcceptableHttpException
     * @throws Yii\web\NotFoundHttpException
     */
    public function actionUpdatePackage($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }
        $packageModel = PackageForm::findOne($id);
        if (!$packageModel) {
            throw new NotFoundHttpException();
        }
        if (!$packageModel->load($request->post())) {
            throw new NotAcceptableHttpException();
        }
        if (!$packageModel->validate()) {
            return ['error' => [
                'message' => 'Model validation error',
                'html' => Ui::errorSummary($packageModel, ['class' => 'alert-danger alert']),
            ]];
        }
        if (!$packageModel->save(false)) {
            throw new NotAcceptableHttpException();
        }
        return [
            'package' => $packageModel,
        ];
    }

    /**
     * Delete Package AJAX action
     * Mark package as deleted
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeletePackage($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }
        $packageModel = PackageForm::findOne($id);
        if (!$packageModel) {
            throw new NotFoundHttpException();
        }
        if ($packageModel->getAttribute('deleted') == $packageModel::DELETED) {
            throw new NotAcceptableHttpException();
        }
        $packageModel->setAttribute('deleted', $packageModel::DELETED);
        if (!$packageModel->save(false)) {
            throw new NotAcceptableHttpException();
        }
        return [
            'package' => $packageModel,
        ];
    }
}