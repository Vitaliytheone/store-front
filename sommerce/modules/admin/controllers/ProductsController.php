<?php

namespace sommerce\modules\admin\controllers;

use common\components\ActiveForm;
use common\components\response\CustomResponse;
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
     * @throws NotAcceptableHttpException
     */
    public function actionCreateProduct()
    {
        $model = new CreateProductForm();
        $model->setUser(Yii::$app->user);

        if (!$model->create(Yii::$app->request->post())) {
            return ['error' => [
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
        /**
         * @var Products $product
         */
        $product = $this->findClassModel($id, Products::class);

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
     * @throws NotFoundHttpException
     */
    public function actionGetProduct($id)
    {
        /**
         * @var CreateProductForm $productModel
         */
        $productModel = $this->findClassModel($id, CreateProductForm::class);

        return [
            'product' => $productModel->getAttributes(),
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
         * @var CreateProductForm $model
         */
        $model = $this->findClassModel($id, CreateProductForm::class);
        $model->setUser(Yii::$app->user);

        if (!$model->edit(Yii::$app->request->post())) {
            return [
                'error' => [
                'message' => 'Model validation error',
                'html' => UiHelper::errorSummary($model, ['class' => 'alert-danger alert']),
                ]
            ];
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
        /**
         * @var MoveProductForm $model
         */
        $model = $this->findClassModel($id, MoveProductForm::class);
        $model->setUser(Yii::$app->user);

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
        $model = new CreatePackageForm();
        $model->setUser(Yii::$app->getUser());

        if (!$model->create(Yii::$app->request->post())) {
            return ['error' => [
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
        /**
         * @var CreatePackageForm $model
         */
        $model = $this->findClassModel($id, CreatePackageForm::class);

        return [
            'package' => $model->getAttributes(),
        ];
    }

    /**
     * Update Package AJAX action
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdatePackage($id)
    {
        /**
         * @var CreatePackageForm $model
         */
        $model = $this->findClassModel($id, CreatePackageForm::class);
        $model->setUser(Yii::$app->user);

        if (!$model->edit(Yii::$app->request->post())) {
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
         * @var MovePackageForm $model
         */
        $model = $this->findClassModel($id, MovePackageForm::class);
        $model->setUser(Yii::$app->user);
        $newPosition = $model->changePosition($position);

        if ($newPosition === false) {
            throw new NotAcceptableHttpException();
        }

        return ['position' => $newPosition];
    }
}