<?php

namespace sommerce\modules\admin\controllers;

use common\components\ActiveForm;
use common\models\store\ActivityLog;
use common\models\store\Packages;
use common\models\store\Products;
use common\models\stores\StoreAdminAuth;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\EditNavigationForm;
use sommerce\modules\admin\models\forms\MovePackageForm;
use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\models\forms\CreateProductForm;
use sommerce\modules\admin\models\forms\CreatePackageForm;
use common\models\stores\StoreProviders;
use common\helpers\ApiProviders;
use sommerce\modules\admin\models\forms\MoveProductForm;
use sommerce\modules\admin\models\search\ProductsSearch;


/**
 * Class ProductsController
 * @package sommerce\modules\admin\controllers
 */
class ProductsController extends CustomController
{
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
        $this->view->title = Yii::t('admin', 'products.page_title');

        $search = new ProductsSearch();
        $search->setStore($this->store);

        $this->addModule('adminProductsList');
        $this->addModule('adminProductEdit', [
            'products' => $search->getProductsProperties(),
            'confirmMenu' => [
                'url' => Url::toRoute('/products/create-product-menu'),
                'labels' => [
                    'title' => Yii::t('admin', 'products.product_menu_header'),
                    'message' => Yii::t('admin', 'products.product_menu_message'),
                    'confirm_button' => Yii::t('admin', 'products.product_menu_success'),
                    'cancel_button' => Yii::t('admin', 'products.product_menu_cancel'),
                ]
            ]
        ]);
        $this->addModule('adminPackageEdit');
        return $this->render('index', [
            'storeProviders' => $search->getStoreProviders(),
            'products' => $search->getProductsPackages(),
            'store' => $this->store,
        ]);
    }

    /**
     * Create new Product AJAX action
     * @return array
     * @throws \yii\web\NotAcceptableHttpException
     */
    public function actionCreateProduct()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = new CreateProductForm();
        $model->setUser(Yii::$app->user);

        if (!$model->create($request->post())) {
            return $response->data = ['error' => [
                'message' => 'Model validation error',
                'html' => UiHelper::errorSummary($model, ['class' => 'alert-danger alert']),
            ]];
        }

        UiHelper::message(Yii::t('admin', 'products.message_product_created'));

        return [
            'product' => $model->getAttributes(),
        ];
    }

    /**
     * Create product menu item
     * @param integer $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCreateProductMenu($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!($product = Products::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $model = new EditNavigationForm();
        $model->setUser(Yii::$app->user);

        if ($model->create([$model->formName() => [
            'name' => $product->name,
            'link' => EditNavigationForm::LINK_PRODUCT,
            'link_id' => $product->id
        ]])) {
            UiHelper::message(Yii::t('admin', 'settings.nav_message_created'));
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
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

        $model = CreateProductForm::findOne($id);
        $model->setUser(Yii::$app->user);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->edit($request->post())) {
            return $response->data = ['error' => [
                'message' => 'Model validation error',
                'html' => UiHelper::errorSummary($model, ['class' => 'alert-danger alert']),
            ]];
        };

        UiHelper::message(Yii::t('admin', 'products.message_product_updated'));

        return [
            'product' => $model->getAttributes(),
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

        $model = MoveProductForm::findOne($id);
        $model->setUser(Yii::$app->user);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $newPosition = $model->changePosition($position);

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

        $model = new CreatePackageForm();
        $model->setUser(Yii::$app->getUser());

        if (!$model->create($request->post())) {
            return $response->data = ['error' => [
                'message' => 'Model validation error',
                'html' => UiHelper::errorSummary($model, ['class' => 'alert-danger alert']),
            ]];
        }

        UiHelper::message(Yii::t('admin', 'products.message_package_created'));

        return [
            'package' => $model->getAttributes(),
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

        $model = CreatePackageForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return [
            'package' => $model->getAttributes(),
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

        $model = CreatePackageForm::findOne($id);
        $model->setUser(Yii::$app->user);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->edit($request->post())) {
            return ['error' => [
                'message' => 'Model validation error',
                'html' => UiHelper::errorSummary($model, ['class' => 'alert-danger alert']),
            ]];
        }

        UiHelper::message(Yii::t('admin', 'products.message_package_updated'));

        return [
            'package' => $model,
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
        
        /* @var $storeProviders \common\models\stores\StoreProviders[] */
        $storeProvider = StoreProviders::findOne([
            'provider_id' => $provider_id,
            'store_id' => $this->store->id
        ]);

        if (!$storeProvider) {
            throw new NotFoundHttpException();
        }

        $providerApi = new ApiProviders($storeProvider);

        $providerServices = $providerApi->services(['Default']);

        return $providerServices;
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

        $model = Packages::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->deleteVirtual()) {
            throw new NotAcceptableHttpException();
        }
        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_DELETED, $model->id, $model->id);

        UiHelper::message(Yii::t('admin', 'products.message_package_deleted'));

        return [
            'package' => $model->getAttributes(),
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

        $model = MovePackageForm::findOne($id);
        $model->setUser(Yii::$app->user);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $newPosition = $model->changePosition($position);

        if ($newPosition === false) {
            throw new NotAcceptableHttpException();
        }

        return ['position' => $newPosition];
    }

}