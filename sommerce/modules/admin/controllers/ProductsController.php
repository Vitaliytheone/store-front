<?php

namespace sommerce\modules\admin\controllers;

use admin\models\forms\package\EditPackageForm;
use admin\models\forms\product\EditProductForm;
use common\components\ActiveForm;
use common\components\response\CustomResponse;
use common\models\sommerce\ActivityLog;
use common\models\sommerce\Packages;
use common\models\sommerce\Products;
use common\models\sommerces\StoreAdminAuth;
use admin\models\forms\package\MovePackageForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use sommerce\helpers\UiHelper;
use admin\models\forms\package\CreatePackageForm;
use common\models\sommerces\StoreProviders;
use common\helpers\ApiProviders;
use admin\models\forms\product\MoveProductForm;
use sommerce\modules\admin\models\search\ProductsSearch;
use admin\models\forms\product\CreateProductForm;

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
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'create-product',
                    'move-package',
                    'delete-package',
                    'get-provider-services',
                    'update-package',
                    'get-package',
                    'create-package',
                    'move-product',
                    'update-product',
                    'get-product',
                    'create-product-menu',
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-product' => ['POST'],
                    'move-package' => ['POST'],
                    'delete-package' => ['POST'],
                    'get-provider-services' => ['GET'],
                    'update-package' => ['POST'],
                    'get-package' => ['GET'],
                    'create-package' => ['POST'],
                    'move-product' => ['POST'],
                    'update-product' => ['POST'],
                    'get-product' => ['GET'],
                    'create-product-menu' => ['POST'],
                ],
            ],
            'jqueryApi' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'create-product',
                    'move-package',
                    'delete-package',
                    'get-provider-services',
                    'update-package',
                    'get-package',
                    'move-product',
                    'update-product',
                    'get-product',
                    'create-product-menu',
                ],
                'formats' => [
                    'application/json' => CustomResponse::FORMAT_JSON,
                ],
            ],
        ]);
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

        $this->addModule('adminProducts');

        return $this->render('index', [
            'storeProviders' => $search->getStoreProviders(),
            'products' => $search->getProductsPackages(),
            'store' => $this->store,
        ]);
    }

    /**
     * Create new Product AJAX action
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionCreateProduct()
    {
        $model = new CreateProductForm();
        $model->setUser(Yii::$app->user->getIdentity());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'products.message_product_created'));

            return [
                'status' => 'success',
                'product' => $model->getAttributes(),
            ];
        }

        return [
            'status' => 'error',
            'error' => ActiveForm::firstError($model),
        ];
    }

    /**
     * Update Product AJAX action
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateProduct($id)
    {
        /**
         * @var Products $model
         */
        $product = $this->findClassModel($id, Products::class);

        $model = new EditProductForm();
        $model->setUser(Yii::$app->user->getIdentity());
        $model->setProduct($product);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'products.message_product_updated'));

            return [
                'status' => 'success',
                'product' => $model->getAttributes(),
            ];
        }

        return [
            'status' => 'error',
            'error' => ActiveForm::firstError($model),
        ];
    }

    /**
     * Get Product AJAX action
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetProduct($id)
    {
        /**
         * @var Products $productModel
         */
        $productModel = $this->findClassModel($id, Products::class);

        return [
            'status' => 'success',
            'product' => $productModel->getAttributes(),
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
        /**
         * @var Products $product
         */
        $product = $this->findClassModel($id, Products::class);

        $model = new MoveProductForm();
        $model->setProduct($product);
        $model->setUser(Yii::$app->user->getIdentity());

        $newPosition = $model->changePosition($position);

        if ($newPosition === false) {
            return [
                'status' => 'error',
                'error' => ActiveForm::firstError($model),
            ];
        }

        return [
            'status' => 'success',
            'position' => $newPosition,
        ];
    }

    /**
     * Create new Package AJAX action
     * @param integer $id
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function actionCreatePackage($id)
    {
        /**
         * @var Products $product
         */
        $product = $this->findClassModel($id, Products::class);

        $model = new CreatePackageForm();
        $model->setProduct($product);
        $model->setUser(Yii::$app->user->getIdentity());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'products.message_package_created'));

            return [
                'status' => 'success',
                'product' => $model->getAttributes(),
            ];
        }

        return [
            'status' => 'error',
            'error' => ActiveForm::firstError($model),
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
        /**
         * @var Packages $package
         */
        $package = $this->findClassModel($id, Packages::class);

        return [
            'status' => 'success',
            'package' => $package->getAttributes(),
        ];
    }

    /**
     * Update Package AJAX action
     * @param integer $id
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdatePackage($id)
    {
        /**
         * @var Packages $package
         */
        $package = $this->findClassModel($id, Packages::class);

        $model = new EditPackageForm();
        $model->setPackage($package);
        $model->setUser(Yii::$app->user->getIdentity());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'products.message_package_updated'));

            return [
                'status' => 'success',
                'product' => $model->getAttributes(),
            ];
        }

        return [
            'status' => 'error',
            'error' => ActiveForm::firstError($model),
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
        /**
         * @var StoreProviders $storeProvider
         */
        $storeProvider = $this->findClassModel([
            'provider_id' => $provider_id,
            'store_id' => $this->store->id
        ], StoreProviders::class);

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
        /**
         * @var Packages $model
         */
        $model = $this->findClassModel($id, Packages::class);
        if (!$model->deleteVirtual()) {
            throw new NotAcceptableHttpException();
        }
        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_DELETED, $model->id, $model->id);

        UiHelper::message(Yii::t('admin', 'products.message_package_deleted'));

        return [
            'status' => 'success',
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
        /**
         * @var Packages $package
         */
        $package = $this->findClassModel($id, Packages::class);

        $model = new MovePackageForm();
        $model->setProduct($package);
        $model->setUser(Yii::$app->user->getIdentity());

        $newPosition = $model->changePosition($position);

        if ($newPosition === false) {
            return [
                'status' => 'error',
                'error' => ActiveForm::firstError($model),
            ];
        }

        return [
            'status' => 'success',
            'position' => $newPosition,
        ];
    }
}