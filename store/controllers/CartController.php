<?php

namespace store\controllers;

use common\components\ActiveForm;
use common\components\filters\DisableCsrfToken;
use common\models\store\Carts;
use common\models\store\Packages;
use store\helpers\UserHelper;
use store\models\forms\AddToCartForm;
use store\models\forms\OrderForm;
use store\models\search\CartSearch;
use Yii;
use yii\base\UnknownClassException;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
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
        return ArrayHelper::merge(parent::behaviors(), [
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
            'token' => [
                'class' => DisableCsrfToken::class,
                'only' => ['index'],
            ],
        ]);
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['index'])) {
            $this->enableDomainValidation = false;
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Displays cart.
     * @return string|Response
     * @throws UnknownClassException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $this->pageTitle = Yii::t('app', 'cart.title');

        Url::remember();
        

        $searchModel = new CartSearch();
        $searchModel->setStore($this->store);

        $items = $searchModel->getItemsForView();

        $model = new OrderForm();
        $model->setStore($this->store);
        $model->setSearchItems($searchModel);

        $payload = null;
        $request = Yii::$app->request;

        if ($request->isPost) {
            $payload = $request->post();
        } elseif ($request->get('method')) {
            $payload = [
                $model->formName() => [
                    'email' => $request->get('email'),
                    'method' => $request->get('method'),
                    'fields' => $request->get(),
                ],
            ];
        }

        if ($model->load($payload) && $model->save()) {
            if ($model->redirect) {
                return $this->redirect($model->redirect);
            }
            if ($model->refresh) {
                return $this->refresh();
            }
            return $this->renderPartial('checkout', $model->formData);
        }

        if (!empty($items)) {
            $this->addModule('cartFrontend', [
                'fieldOptions' => $model->getPaymentsFields(),
                'options' => $model->getJsOptions(),
                'cartTotal' => [
                    'amount' => $searchModel->getTotal(),
                    'currency' => $this->store->currency,
                ]
            ]);

            $payments = $model->getPaymentsMethodsForView();
        }

        return $this->render('cart.twig', [
            'cart' => [
                'orders' => $items,
                'total_price' => $searchModel->getTotal(),
                'payments' => $payments ?? [],
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
     * Validate cart
     * @return array
     * @throws UnknownClassException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionValidate()
    {
        $searchModel = new CartSearch();
        $searchModel->setStore($this->store);
        $model = new OrderForm();
        $model->setStore($this->store);
        $model->setSearchItems($searchModel);

        $status = 'error';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $status = 'success';
        }

        return [
            'status' => $status
        ];
    }

    /**
     * Delete cart item
     * @param string|integer $key
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
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
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionOrder($id)
    {
        $package = $this->_findPackage($id);

        $this->pageTitle = $package->name;

        $model = new AddToCartForm();
        $model->setPackage($package);
        $model->setStore($this->store);

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