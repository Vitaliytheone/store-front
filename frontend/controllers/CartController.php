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
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Cart controller
 */
class CartController extends CustomController
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->pageTitle = Yii::t('app', 'cart_title');

        Url::remember();

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $searchModel = new CartSearch();
        $searchModel->setStore($store);

        $items = $searchModel->getItemsForView();;

        $model = new OrderForm();
        $model->setStore($store);
        $model->setSearchItems($searchModel);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->redirect) {
                return $this->redirect($model->redirect);
            }
            return $this->renderPartial('checkout', $model->formData);
        }

        return $this->render('cart.twig', [
            'cart' => [
                'orders' => $items,
                'total_price' => PriceHelper::prepare($searchModel->getTotal(), $store->currency),
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
        $this->pageTitle = Yii::t('app', 'order_title');

        $package = $this->_findPackage($id);

        $model = new AddToCartForm();
        $model->setPackage($package);
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect('/cart');
        }

        return $this->render('order.twig', [
            'package' => [
                'id' => $package->id,
                'name' => Html::encode($package->name),
                'quantity' => $package->quantity,
                'price' => $package->price,
            ],
            'goBackUrl' => $this->getGoBackUrl(),
            'data' => $model->attributes,
            'error' => $model->hasErrors(),
            'errorMessage' => ActiveForm::firstError($model)
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
