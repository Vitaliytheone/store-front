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
use frontend\modules\admin\models\forms\CreateProductForm;
use frontend\modules\admin\models\forms\CreatePackageForm;
use common\models\stores\Stores;
use common\models\stores\StoreProviders;
use common\models\stores\Providers;
use common\helpers\ApiProviders;

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
        /** @var $store Stores */
        $store = yii::$app->store->getInstance();
        return $this->render('index', [
            'storeProviders' => $store->storeProviders,
            'products' => CreateProductForm::getProductsPackages(),
        ]);
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
        $productModel = new CreateProductForm();
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

        $productModel = CreateProductForm::findOne($id);
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
        $productModel = CreateProductForm::findOne($id);
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
     * Move product AJAX action
     * @param $id
     * @param $position
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionMoveProduct($id, $position)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }
        $productModel = CreateProductForm::findOne($id);
        if (!$productModel) {
            throw new NotFoundHttpException();
        }
        $newPosition = $productModel->changePosition($position);
        if ($newPosition === false) {
            throw new NotAcceptableHttpException();
        }
        return ['position' => $newPosition];
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

        $packageModel = new CreatePackageForm();
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

        $packageModel = CreatePackageForm::findOne($id);
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
        $packageModel = CreatePackageForm::findOne($id);
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
     * Get provider`s services list AJAX action
     * @param $provider_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetProviderServices($provider_id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }

        /* @var $store Stores */
        /* @var $storeProviders \common\models\stores\StoreProviders[] */
        $store = yii::$app->store->getInstance();
        $storeProvider = StoreProviders::findOne([
            'provider_id' => $provider_id,
            'store_id' => $store->id
        ]);
        if (!$storeProvider) {
            throw new NotFoundHttpException();
        }
        $provider = $storeProvider->provider;
        if (!$provider) {
            throw new NotFoundHttpException();
        }
        $providerApi = new ApiProviders($provider->site, $storeProvider->apikey);
        $providerServices = $providerApi->services(['Default']);

        return [
            'services' => $providerServices
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
        $packageModel = CreatePackageForm::findOne($id);
        if (!$packageModel) {
            throw new NotFoundHttpException();
        }
        if ($packageModel->getAttribute('deleted') == $packageModel::DELETED) {
            throw new NotAcceptableHttpException();
        }
        if (!$packageModel->deleteVirtual()) {
            throw new NotAcceptableHttpException();
        };
        return [
            'package' => $packageModel,
        ];
    }

    /**
     * Move package AJAX action
     * @param $id
     * @param $position
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionMovePackage($id, $position)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        if (!$request->isAjax) {
            exit;
        }
        $packageModel = CreatePackageForm::findOne($id);
        if (!$packageModel) {
            throw new NotFoundHttpException();
        }
        $newPosition = $packageModel->changePosition($position);
        if ($newPosition === false) {
            throw new NotAcceptableHttpException();
        }
        return ['position' => $newPosition];
    }


}