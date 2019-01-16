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
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\models\forms\CreateProductForm;
use sommerce\modules\admin\models\forms\CreatePackageForm;
use common\models\stores\Stores;
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
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'create-product' => ['POST'],
                    'update-product' => ['GET', 'POST'],
                    'move-product' => ['POST'],
                    'move-package' => ['POST'],
                    'create-product-menu' => ['POST'],
                    'create-package' => ['POST'],
                    'update-package' => ['POST'],
                    'delete-package' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'index',
                    'create-product',
                    'update-product',
                    'move-product',
                    'move-package',
                    'create-product-menu',
                    'create-package',
                    'update-package',
                    'delete-package',
                ]
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'index',
                    'create-product',
                    'update-product',
                    'move-product',
                    'move-package',
                    'create-product-menu',
                    'create-package',
                    'update-package',
                    'delete-package',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }

    public function beforeAction($action)
    {
        // Add custom JS modules
        $this->addModule('ordersDetails');

        return parent::beforeAction($action);
    }

    /**
     * Render found products-packages list
     * @return array
     */
    public function actionIndex()
    {
        $search = new ProductsSearch();
        $search->setStore($this->store);
        $data = $search->getProductsPackages();

        return static::apiResponseSuccess($data);
    }

    /**
     * Create new Product AJAX action
     * @return array
     * @throws \Throwable
     */
    public function actionCreateProduct()
    {
        $request = Yii::$app->getRequest();

        $model = new CreateProductForm();
        $model->setUser(Yii::$app->user);

        if (!$model->create($request->post())) {
            Yii::error($model->firstErrors);
            return static::apiResponseError($model->firstErrors);
        }

        UiHelper::message(Yii::t('admin', 'products.message_product_created'));

        $data = $model->getAttributes();

        return static::apiResponseSuccess($data);
    }

    /**
     * Create product menu item
     * @param $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateProductMenu($id)
    {
        if (!($product = Products::findOne($id))) {
            Yii::error('Create product menu: model not found');
            return static::apiResponseError();
        }

        $model = new EditNavigationForm();
        $model->setUser(Yii::$app->user);

        if ($model->create([$model->formName() => [
            'name' => $product->name,
            'link' => EditNavigationForm::LINK_PRODUCT,
            'link_id' => $product->id
        ]])) {
            UiHelper::message(Yii::t('admin', 'settings.nav_message_created'));
            return static::apiResponseSuccess();
        } else {
            return static::apiResponseError($model->firstErrors);
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
     * @param int $id
     * @return array
     */
    public function actionUpdateProduct($id)
    {
        $request = Yii::$app->getRequest();

        // TODO return date from db in case POST-query

        $model = CreateProductForm::findOne($id);
        if (!$model) {
            Yii::error('Update product: model not found');
            return static::apiResponseError();
        }

        $model->setUser(Yii::$app->user);

        if (!$model->edit($request->post())) {
            Yii::error($model->firstErrors);
            return static::apiResponseError($model->firstErrors);
        };

        UiHelper::message(Yii::t('admin', 'products.message_product_updated'));

        $data = $model->getAttributes();

        return static::apiResponseSuccess($data);
    }

    /**
     * Move product AJAX action
     * @param int $id
     * @return array
     */
    public function actionMoveProduct($id)
    {
        $request = Yii::$app->request;

        $model = MoveProductForm::findOne($id);
        if (!$model) {
            Yii::error('Change product position: model not found');
            return static::apiResponseError();
        }

        $model->setUser(Yii::$app->user);

        $newPosition = $model->changePosition($request->post('newIndex'));

        if ($newPosition === false) {
            Yii::error('Change product position: bad data');
            return static::apiResponseError();
        }

        return static::apiResponseSuccess();
    }

    /**
     * Create new Package AJAX action
     * @return array
     * @throws \Throwable
     */
    public function actionCreatePackage()
    {
        $request = Yii::$app->getRequest();

        $model = new CreatePackageForm();
        $model->setUser(Yii::$app->getUser());

        if (!$model->create($request->post())) {
            return static::apiResponseError($model->firstErrors);
        }

        UiHelper::message(Yii::t('admin', 'products.message_package_created'));

        $data = $model->getAttributes();

        return static::apiResponseSuccess($data);
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
     * @param int $id
     * @return array
     */
    public function actionUpdatePackage($id)
    {
        $request = Yii::$app->getRequest();

        $model = CreatePackageForm::findOne($id);
        if (!$model) {
            Yii::error('Update package: model not found');
            return static::apiResponseError();
        }

        $model->setUser(Yii::$app->user);

        if (!$model->edit($request->post())) {
            return static::apiResponseError($model->firstErrors);
        }

        UiHelper::message(Yii::t('admin', 'products.message_package_updated'));

        $data = $model->getAttributes();

        return static::apiResponseSuccess($data);
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
     * @throws \Throwable
     */
    public function actionDeletePackage($id)
    {
        $request = Yii::$app->getRequest();

        $model = Packages::findOne($id);

        if (!$model) {
            Yii::error('Delete package: model not found');
            return static::apiResponseError();
        }

        if (!$model->deleteVirtual()) {
            Yii::error('Delete package: delete error');
            return static::apiResponseError();
        }
        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);
        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_DELETED, $model->id, $model->id);

        UiHelper::message(Yii::t('admin', 'products.message_package_deleted'));

        return static::apiResponseSuccess();
    }

    /**
     * Move package AJAX action
     * @param int $id
     * @return array
     */
    public function actionMovePackage($id)
    {
        $request = Yii::$app->request;

        $model = MovePackageForm::findOne($id);
        if (!$model) {
            Yii::error('Change package position: model not found');
            return static::apiResponseError();
        }

        $model->setUser(Yii::$app->user);
        $newPosition = $model->changePosition($request->post('newIndex'));

        if ($newPosition === false) {
            Yii::error('Change package position: bad data');
            return static::apiResponseError();
        }

        return static::apiResponseSuccess();
    }

    /**
     * @param string|array $error
     * @return array
     */
    public static function apiResponseError($error = 'Internal error')
    {
        if (is_array($error)) {
            $error = !empty($firstErrors) ? reset($firstErrors) : 'Internal error';
        }

        return [
            'success' => false,
            'error_message' => $error,
        ];
    }

    /**
     * Api response success
     * @param array|integer|string $data Response data array
     * @return array
     */
    public static function apiResponseSuccess($data = [])
    {
        return [
            'success' => true,
            'data' => $data
        ];
    }
}