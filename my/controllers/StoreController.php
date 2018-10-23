<?php

namespace my\controllers;

use common\models\panels\Auth;
use common\models\panels\Customers;
use common\models\panels\Orders;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreAdmins;
use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\components\ActiveForm;
use my\models\forms\CreateStoreStaffForm;
use my\models\forms\EditStoreDomainForm;
use my\models\forms\EditStoreStaffForm;
use my\models\forms\OrderStoreForm;
use my\models\forms\SetStoreStaffPasswordForm;
use my\models\search\StoresSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;

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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'edit-domain' => ['POST'],
                    'staff-create' => ['POST'],
                    'staff-edit' => ['POST'],
                    'staff-password' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['edit-domain', 'staff-create', 'staff-edit', 'staff-password'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
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
     * @return string|Response
     * @throws \Throwable
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

        if (!$model->load($request->post()) || !$model->orderStore()) {
            return $this->render('order', [
                'model' => $model,
            ]);
        }

        return $this->redirect('/invoices/' . $model->getInvoiceCode());
    }

    /**
     * Edit store domain
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionEditDomain($id)
    {
        $store = $this->_findStore($id);

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
            'customer' => $user,
            'last_update' => $storeDomain ? $storeDomain->updated_at : null
        ])) {
            return [
                'status' => 'error',
                'message' => Yii::t('app', 'error.store.can_not_change_domain')
            ];
        }

        /**
         * @var Auth $user
         */
        $user = Yii::$app->user->getIdentity();

        $model = new EditStoreDomainForm();
        $model->setStore($store);
        $model->setUser($user);

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
     * Prolongation store
     * @param int $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionProlong($id)
    {
        throw new ForbiddenHttpException();

        $store = $this->_findStore($id);

        /**
         * @var Customers $user
         */
        $user = Yii::$app->user->getIdentity();

        if (!Stores::hasAccess($store, Stores::CAN_PROLONG, [
            'customer' => $user,
        ])) {
            throw new ForbiddenHttpException();
        }

        if (!($code = $store->prolong())) {
            return $this->redirect('/stores');
        }

        return $this->redirect('/invoices/' . $code);
    }

    /**
     * Render store staff list
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionStaff($id)
    {
        $this->view->title = Yii::t('app', 'pages.title.store.staff');
        $store = $this->_findStore($id);

        if ($store->status != Stores::STATUS_ACTIVE) {
            return $this->redirect('/stores');
        }

        $staffs = StoreAdmins::find()->where(['store_id' => $store->id])->orderBy(['id' => SORT_DESC])->all();

        return $this->render('staff', [
            'store' => $store,
            'staffs' => $staffs,
            'canCreate' => Stores::hasAccess($store, Stores::CAN_STAFF_CREATE, [
                'customer' => Yii::$app->user->getIdentity(),
            ]),
        ]);
    }

    /**
     * Create store staff ajax action
     * @param int $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionStaffCreate($id)
    {
        $store = $this->_findStore($id);

        if (!Stores::hasAccess($store, Stores::CAN_STAFF_CREATE, [
            'customer' => Yii::$app->user->getIdentity(),
        ])) {
            throw new ForbiddenHttpException();
        }

        $form = new CreateStoreStaffForm();
        $form->setStore($store);

        if ($form->load(Yii::$app->request->post())) {

            if (!$form->save()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($form)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => Yii::t('app', 'error.stores.staff.can_not_create')
        ];
    }

    /**
     * Edit store staff ajax action
     * @param int $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionStaffEdit($id)
    {
        $staff = StoreAdminAuth::findOne([
            'id' => $id,
        ]);

        if (!$staff) {
            throw new ForbiddenHttpException();
        }

        $store = $this->_findStore($staff->store_id);

        if (!Stores::hasAccess($store, Stores::CAN_STAFF_EDIT, [
            'customer' => Yii::$app->user->getIdentity(),
        ])) {
            throw new ForbiddenHttpException();
        }

        $model = new EditStoreStaffForm();
        $model->setStaff($staff);

        if ($model->load(Yii::$app->request->post())) {

            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => Yii::t('app', 'error.stores.staff.can_not_edit')
        ];
    }

    /**
     * Update store staff password ajax action
     * @param int $id
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionStaffPassword($id)
    {
        $staff = StoreAdminAuth::findOne([
            'id' => $id,
        ]);

        if (!$staff) {
            throw new ForbiddenHttpException();
        }

        $store = $this->_findStore($staff->store_id);

        if (!Stores::hasAccess($store, Stores::CAN_STAFF_UPDATE_PASSWORD, [
            'customer' => Yii::$app->user->getIdentity(),
        ])) {
            throw new ForbiddenHttpException();
        }

        $model = new SetStoreStaffPasswordForm();
        $model->setStaff($staff);

        if ($model->load(Yii::$app->request->post())) {

            if (!$model->save()) {
                return [
                    'status' => 'error',
                    'error' => ActiveForm::firstError($model)
                ];
            }

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'error' => Yii::t('app', 'error.stores.staff.can_not_change_password')
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
