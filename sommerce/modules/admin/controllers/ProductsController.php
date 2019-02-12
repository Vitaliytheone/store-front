<?php

namespace sommerce\modules\admin\controllers;

use common\components\response\CustomResponse;
use common\models\store\ActivityLog;
use common\models\store\Packages;
use common\models\store\Products;
use common\models\stores\StoreAdminAuth;
use my\components\ActiveForm;
use sommerce\modules\admin\models\forms\EditNavigationForm;
use sommerce\modules\admin\models\forms\MovePackageForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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
                    'create-product' => ['POST', 'GET'],
                    'update-product' => ['GET', 'POST'],
                    'move-product' => ['POST', 'GET'],
                    'move-package' => ['POST', 'GET'],
                    'create-product-menu' => ['POST', 'GET'],
                    'create-package' => ['POST', 'GET'],
                    'update-package' => ['GET', 'POST'],
                    'delete-package' => ['POST', 'GET'],
                    'get-provider-services' => ['GET'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'list',
                    'create-product',
                    'update-product',
                    'move-product',
                    'move-package',
                    'create-product-menu',
                    'create-package',
                    'update-package',
                    'delete-package',
                    'get-provider-services',
                ]
            ],
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
                    'application/json' => CustomResponse::FORMAT_AJAX_API,
                ],
            ],
        ]);
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
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

        $endPoints = [
            "add_listing" => Url::toRoute(['products/list']),
            "add_product" => Url::toRoute(['products/create-product']),
            "confirm_add_product" => Url::toRoute(['products/create-product-menu', 'id' => '']),
            "add_package" => Url::toRoute(['products/create-package']),
            "get_update_product" => Url::toRoute(['products/update-product', 'id' => '']),
            "update_product" => Url::toRoute(['products/update-product', 'id' => '']),
            "get_update_package" => Url::toRoute(['products/update-package', 'id' => '']),
            "update_package" => Url::toRoute(['products/update-package', 'id' => '']),
            "change_position_product" => Url::toRoute(['products/move-product', 'id' => '']),
            "change_position_package" => Url::toRoute(['products/move-package', 'id' => '']),
            "delete_package" => Url::toRoute(['products/delete-package', 'id' => '']),
            "get_providers" => Url::toRoute(['products/get-provider-services', 'id' => '']),
        ];

        return $this->render('index', [
            'endPoints' => $endPoints,
        ]);
    }

    /**
     * @return array
     */
    public function actionList()
    {
        $search = new ProductsSearch();
        $search->setStore($this->store);
        $data = $search->getProductsPackages();

        return $data;
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
            throw new BadRequestHttpException(!empty($model->firstErrors) ? $model->firstErrors : 'Product cannot save!');
        }

        $data = $model->getData();

        return $data;
    }

    /**
     * Create product menu item
     * @param $id
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateProductMenu($id)
    {
        if (!($product = Products::findOne($id))) {
            Yii::error('Create product menu: model not found');
            throw new NotFoundHttpException('Product not found!');
        }

        $model = new EditNavigationForm();
        $model->setUser(Yii::$app->user);

        if ($model->create([$model->formName() => [
            'name' => $product->name,
            'link' => EditNavigationForm::LINK_PRODUCT,
            'link_id' => $product->id
        ]])) {
            return true;
        } else {
            throw new BadRequestHttpException();
        }
    }

    /**
     * Update Product AJAX action
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionUpdateProduct($id)
    {
        $request = Yii::$app->getRequest();

        if ($request->method === 'GET') {
            $productModel = CreateProductForm::findOne($id);

            if (!$productModel) {
                Yii::error('Update product: model not found');
                throw new NotFoundHttpException('Product not found!');
            }

            $data = $productModel->getAttributes();

            return $data;
        }

        $model = CreateProductForm::findOne($id);
        if (!$model) {
            Yii::error('Update product: model not found');
            throw new NotFoundHttpException('Product not found!');
        }

        $model->setUser(Yii::$app->user);

        if (!$model->edit($request->post())) {
            Yii::error($model->firstErrors);
            throw new BadRequestHttpException('Product cannot save!');
        };

        $data = $model->getAttributes();

        return $data;
    }

    /**
     * Move product AJAX action
     * @param $id
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionMoveProduct($id)
    {
        $request = Yii::$app->request;

        $model = MoveProductForm::findOne($id);
        if (!$model) {
            Yii::error('Change product position: model not found');
            throw new NotFoundHttpException('Product not found!');
        }

        $model->setUser(Yii::$app->user);

        if (!$model->changePosition($request->post())) {
            Yii::error('Change product position: ' . ActiveForm::firstError($model));
            throw new BadRequestHttpException();
        }

        return true;
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
            throw new BadRequestHttpException('Package cannot save!');
        }

        $data = $model->getAttributes();

        return $data;
    }

    /**
     * Update Package AJAX action
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdatePackage($id)
    {
        $request = Yii::$app->getRequest();

        if ($request->method === 'GET') {
            $model = CreatePackageForm::findOne($id);

            if (!$model) {
                Yii::error('Update package: model not found');
                throw new NotFoundHttpException('Package not found!');
            }

            $data = $model->getAttributes();

            return $data;
        }

        $model = CreatePackageForm::findOne($id);
        if (!$model) {
            Yii::error('Update package: model not found');
            throw new NotFoundHttpException('Package not found!');
        }

        $model->setUser(Yii::$app->user);

        if (!$model->edit($request->post())) {
            throw new BadRequestHttpException('Package cannot save!');
        }

        $data = $model->getAttributes();

        return $data;
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
            throw new NotFoundHttpException();
        }

        $providerApi = new ApiProviders($storeProvider);

        $providerServices = $providerApi->services(['Default']);

        return $providerServices;
    }

    /**
     * Delete Package AJAX action
     * @param $id
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDeletePackage($id)
    {
        $model = Packages::findOne($id);

        if (!$model) {
            Yii::error('Delete package: model not found');
            throw new NotFoundHttpException('Package not found!');
        }

        if (!$model->deleteVirtual()) {
            Yii::error('Delete package: delete error');
            throw new BadRequestHttpException();
        }
        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);
        ActivityLog::log($identity, ActivityLog::E_PACKAGES_PACKAGE_DELETED, $model->id, $model->id);

        return true;
    }

    /**
     * Move package AJAX action
     * @param $id
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionMovePackage($id)
    {
        $request = Yii::$app->request;

        $model = MovePackageForm::findOne($id);
        if (!$model) {
            Yii::error('Change package position: model not found');
            throw new NotFoundHttpException('Package not found!');
        }

        $model->setUser(Yii::$app->user);

        if (!$model->changePosition($request->post())) {
            Yii::error('Change package position: ' . ActiveForm::firstError($model));
            throw new BadRequestHttpException();
        }

        return true;
    }
}
