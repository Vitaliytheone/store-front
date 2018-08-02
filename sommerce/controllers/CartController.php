<?php
namespace sommerce\controllers;

use common\components\ActiveForm;
use common\models\store\Carts;
use common\models\store\Packages;
use common\models\stores\Stores;
use sommerce\helpers\UserHelper;
use sommerce\models\forms\AddToCartForm;
use sommerce\models\forms\OrderForm;
use sommerce\models\search\CartSearch;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;


/**
 * Cart controller
 */
class CartController extends CustomController
{
    public function behaviors()
    {
        return [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['validate']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'validate'=> ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['validate'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->pageTitle = Yii::t('app', 'cart.title');

        Url::remember();

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $searchModel = new CartSearch();
        $searchModel->setStore($store);

        $items = $searchModel->getItemsForView();

        $model = new OrderForm();
        $model->setStore($store);
        $model->setSearchItems($searchModel);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->redirect) {
                return $this->redirect($model->redirect);
            }
            if ($model->refresh) {
                return $this->refresh();
            }
            return $this->renderPartial('checkout', $model->formData);
        }

        $this->addModule('cartFrontend', [
            'fieldOptions' => $model->getPaymentsFields(),
            'options' => $model->getJsOptions()
        ]);

        return $this->render('cart.twig', [
            'cart' => [
                'orders' => $items,
                'total_price' => $searchModel->getTotal(),
                'payments' => $model->getPaymentsMethodsForView(),
                'form' => [
                    'selected_method' => $model->method,
                    'email' => $model->email,
                ]
            ],

            'error' => $model->hasErrors(),
            'error_message' => ActiveForm::firstError($model)
        ]);
    }


    public function actionValidate() {
        $store = Yii::$app->store->getInstance();
        $searchModel = new CartSearch();
        $searchModel->setStore($store);
        $model = new OrderForm();
        $model->setStore($store);
        $model->setSearchItems($searchModel);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return [
                'status' => 'success'
            ];
        }
        return [
            'status' => 'error'
        ];
    }

    /**
     * Delete cart item
     * @param string|integer $key
     * @return string
     */
    public function actionDelete($key)
    {
        $cartItem = $this->_findCartItem($key);

        UserHelper::removeCartKey($cartItem->key);
        $cartItem->delete();

        return $this->redirect('/cart');
    }

    /**
     * Displays add to cart page.
     * @param integer $id
     * @return string
     */
    public function actionOrder($id)
    {

        $package = $this->_findPackage($id);

        $this->pageTitle = $package->name;

        $model = new AddToCartForm();
        $model->setPackage($package);
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect('/cart');
        }

        return $this->render('order.twig', [
            'order' => [
                'id' => $package->id,
                'name' => Html::encode($package->name),
                'quantity' => $package->quantity,
                'price' => $package->price,
                'back_url' => $this->getGoBackUrl(),
                'form' => [
                    'link' => $model->link,
                ],
            ],

            'error' => $model->hasErrors(),
            'error_message' => ActiveForm::firstError($model),
        ]);
    }

    /**
     * Find package
     * @param integer $id
     * @return Packages
     * @throws NotFoundHttpException
     */
    protected function _findPackage($id)
    {
        $package = null;

        if (empty($id) || !($package = Packages::find()->andWhere([
            'id' => $id,
        ])->active()->one())) {
            throw new NotFoundHttpException();
        }

        return $package;
    }

    /**
     * Find cart item
     * @param string $key
     * @return Carts
     * @throws NotFoundHttpException
     */
    protected function _findCartItem($key)
    {
        $cartItem = null;

        if (empty($key) || !($cartItem = Carts::findOne([
            'key' => $key,
        ]))) {
            throw new NotFoundHttpException();
        }

        return $cartItem;
    }
}
