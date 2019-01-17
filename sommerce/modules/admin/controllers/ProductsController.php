<?php

namespace sommerce\modules\admin\controllers;

use common\models\store\ActivityLog;
use common\models\store\Packages;
use common\models\store\Products;
use common\models\stores\StoreAdminAuth;
use sommerce\modules\admin\models\forms\EditNavigationForm;
use sommerce\modules\admin\models\forms\MovePackageForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
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
                    'list' => ['GET'],
                    'create-product' => ['POST'],
                    'update-product' => ['GET', 'POST'],
                    'move-product' => ['POST'],
                    'move-package' => ['POST'],
                    'create-product-menu' => ['POST'],
                    'create-package' => ['POST'],
                    'update-package' => ['GET', 'POST'],
                    'delete-package' => ['POST'],
                    'get-provider-services' => ['GET'],
                ],
            ],
//            'ajax' => [
//                'class' => AjaxFilter::class,
//                'only' => [
//                    'list',
//                    'create-product',
//                    'update-product',
//                    'move-product',
//                    'move-package',
//                    'create-product-menu',
//                    'create-package',
//                    'update-package',
//                    'delete-package',
//                    'get-provider-services',
//                ]
//            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'create-product',
                    'update-product',
                    'move-product',
                    'move-package',
                    'create-product-menu',
                    'create-package',
                    'update-package',
                    'delete-package',
                    'get-provider-services',
                    'list',
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
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('admin', 'products.page_title');
        $this->layout = '@admin/views/layouts/react_app';

        return $this->render('index');
    }

    /**
     * @return array
     */
    public function actionList()
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
     * Update Product AJAX action
     * @param int $id
     * @return array
     */
    public function actionUpdateProduct($id)
    {
        $request = Yii::$app->getRequest();

        if ($request->method === 'GET') {
            $productModel = CreateProductForm::findOne($id);

            if (!$productModel) {
                Yii::error('Update product: model not found');
                return static::apiResponseError();
            }

            $data = $productModel->getAttributes();

            return static::apiResponseSuccess($data);
        }

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
     * @throws \Throwable
     * @throws \yii\db\Exception
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

        $newPosition = $model->changePosition($request->post());

        if ($newPosition === false) {
            Yii::error('Change product position: bad data');
            return static::apiResponseError($model->firstErrors);
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
     * Update Package AJAX action
     * @param int $id
     * @return array
     */
    public function actionUpdatePackage($id)
    {
        $request = Yii::$app->getRequest();

        if ($request->method === 'GET') {
            $model = CreatePackageForm::findOne($id);

            if (!$model) {
                Yii::error('Update package: model not found');
                return static::apiResponseError();
            }

            $data = $model->getAttributes();

            return static::apiResponseSuccess($data);
        }

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
     * @param int $provider_id
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionGetProviderServices($provider_id)
    {
        /* @var $storeProviders \common\models\stores\StoreProviders[] */
        $storeProvider = StoreProviders::findOne([
            'provider_id' => $provider_id,
            'store_id' => $this->store->id
        ]);

        if (!$storeProvider) {
            Yii::error('Get provider services: provider not found');
            return static::apiResponseError();
        }

        $providerApi = new ApiProviders($storeProvider);

        $providerServices = $providerApi->services(['Default']);

        return static::apiResponseSuccess($providerServices);
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