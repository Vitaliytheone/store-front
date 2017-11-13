<?php
namespace frontend\controllers;

use common\components\ActiveForm;
use common\helpers\PriceHelper;
use common\models\store\Carts;
use common\models\store\Packages;
use common\models\stores\Stores;
use frontend\helpers\UserHelper;
use frontend\models\forms\AddToCartForm;
use frontend\models\forms\OrderForm;
use frontend\models\search\CartSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Cart controller
 */
class CartController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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
        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $searchModel = new CartSearch();
        $searchModel->setStore($store);
        $searchModel->setKeys(UserHelper::getCartKeys());

        $items = $searchModel->search();

        $model = new OrderForm();
        $model->setStore($store);
        $model->setItems($items);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect('/checkout');
        }

        return $this->render('cart', [
            'items' => $items['models'],
            'total' => PriceHelper::prepare($searchModel->getTotal(), $store->currency),
            'data' => $model->attributes,
            'error' => $model->hasErrors(),
            'errorText' => ActiveForm::firstError($model)
        ]);
    }

    /**
     * Displays homepage.
     * @param integer $id
     * @return string
     */
    public function actionRemove($id)
    {
        $cartItem = $this->_findCartItem($id);

        UserHelper::removeCartKey($cartItem->key);
        $cartItem->delete();

        return $this->redirect('/cart');
    }

    /**
     * Displays add to cart page.
     * @param integer $id
     * @return string
     */
    public function actionAddToCart($id)
    {
        $package = $this->_findPackage($id);

        $model = new AddToCartForm();
        $model->setPackage($package);
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect('/cart');
        }

        return $this->render('add_to_cart', [
            'package' => $package,
            'model' => $model,
            'goBackUrl' => $this->getGoBackUrl(),
            'data' => $model->attributes,
            'error' => $model->hasErrors(),
            'errorText' => ActiveForm::firstError($model)
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

        if (empty($id) || !($package = Packages::findOne([
            'id' => $id,
            'visibility' => Packages::VISIBILITY_YES
        ]))) {
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
