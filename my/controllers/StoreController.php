<?php

namespace my\controllers;

use common\models\panels\Auth;
use common\models\panels\Customers;
use common\models\panels\Orders;
use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\components\ActiveForm;
use my\models\forms\EditStoreDomainForm;
use my\models\forms\OrderStoreForm;
use my\models\search\StoresSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class StoreController
 * @package my\controllers
 */
class StoreController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            /**
                             * @var $customer Customers
                             */
                            $customer = Yii::$app->user->getIdentity();

                            if (!$customer || !$customer->can('stores')) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * View Panels list
     * @return string
     */
    public function actionStores()
    {
        $this->view->title = Yii::t('app', 'pages.title.stores');

        /**
         * @var Customers $customer
         */
        $customer = Yii::$app->user->identity;

        $storesSearch = new StoresSearch();
        $storesSearch->setParams([
            'customer_id' => $customer->id,
            'customer' => $customer
        ]);

        return $this->render('stores', [
            'stores' => $storesSearch->search(),
            'accesses' => [
                'canCreate' => Orders::can('create_store', [
                    'customerId' => $customer->id
                ])
            ]
        ]);
    }

    /**
     * Create store order
     * @return string
     */
    public function actionOrder()
    {
        $this->view->title = Yii::t('app', 'pages.title.order');

        /** @var Auth $user */
        $user = Yii::$app->user->getIdentity();

        $request = Yii::$app->request;

        $model = new OrderStoreForm();
        $model->setUser($user);
        $model->setIp($request->getUserIP());
        $model->setTrial(!$user->hasStores());

        if (!$model->load($request->post()) || !$model->orderStore()) {
            return $this->render('order', [
                'model' => $model,
            ]);
        }

        if ($model->getTrial()) {
            return $this->redirect(Url::toRoute('/stores'));
        }

        return $this->redirect('/invoices/' . $model->getInvoiceCode());
    }

    /**
     * Edit store domain
     * @param int $id
     * @return array
     */
    public function actionEditDomain($id)
    {
        $store = $this->_findStore($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        /**
         * @var Customers $user
         */
        $user = Yii::$app->user->getIdentity();
        $storeDomain = StoreDomains::findOne([
            'store_id' => $store->id,
            'type' => [
                StoreDomains::DOMAIN_TYPE_DEFAULT,
                StoreDomains::DOMAIN_TYPE_SUBDOMAIN
            ]
        ]);

        if (!Stores::hasAccess($store, Stores::CAN_DOMAIN_CONNECT, [
            'user' => $user,
            'last_update' => $storeDomain ? $storeDomain->updated_at : null
        ])) {
            return [
                'status' => 'error',
                'message' => Yii::t('app', 'error.store.can_not_change_domain')
            ];
        }

        $model = new EditStoreDomainForm();
        $model->setStore($store);

        if ($model->load(Yii::$app->request->post())) {

            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'message' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('app', 'error.store.can_not_change_domain')
        ];
    }

    /**
     * Find store
     * @param integer $id
     * @return Stores
     * @throws NotFoundHttpException
     */
    protected function _findStore($id)
    {
        $store = Stores::findOne($id);

        if (!$store) {
            throw new NotFoundHttpException();
        }

        return $store;
    }
}
