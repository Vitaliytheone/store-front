<?php

namespace sommerce\controllers;

use common\components\ActiveForm;
use common\components\exceptions\FirstValidationErrorHttpException;
use common\components\filters\DisableCsrfToken;
use common\components\response\CustomResponse;
use common\models\sommerce\Carts;
use common\models\sommerce\Packages;
use sommerce\helpers\UserHelper;
use sommerce\helpers\PriceHelper;
use sommerce\models\forms\AddToCartForm;
use sommerce\models\forms\OrderForm;
use sommerce\models\forms\OrderFormOld;
use sommerce\models\search\CartSearch;
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
                'only' => [
                    'get-order-data',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'get-order-data' => ['GET'],
                    'validate' => ['POST']
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'get-order-data',
                    'validate',
                ],
                'formats' => [
                    'application/json' => CustomResponse::FORMAT_AJAX_API,
                ],
            ],
            'token' => [
                'class' => DisableCsrfToken::class,
                'only' => [
                    'index',
                    'validate'
                ],
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

    public function actionGetOrderData($id)
    {
        $package = $this->_findPackage($id);

        return [
            'id' => $package->id,
            'name' => Html::encode($package->name),
            'price_raw' => $package->price,
            'price' => PriceHelper::getPrice($package->price, $this->store->currency),
        ];
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

        $model = new OrderFormOld();
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
     * Validate order form data AJAX action
     * @return bool
     * @throws FirstValidationErrorHttpException
     */
    public function actionValidate()
    {
        $request = Yii::$app->request->post();

        $form = new OrderForm();
        $form->setStore($this->store);
        $form->setPackage($this->_findPackage(ArrayHelper::getValue($request, ['OrderForm', 'package_id'])));

        if (!$form->load($request) || !$form->validate()) {
            throw new FirstValidationErrorHttpException($form);
        }

        return true;
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
}
