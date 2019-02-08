<?php

namespace my\controllers;

use common\models\panels\Customers;
use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\Auth;
use my\models\forms\OrderDomainForm;
use my\models\search\DomainsSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\filters\VerbFilter;
use my\models\forms\OrderStoreForm;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use my\models\forms\OrderPanelForm;

/**
 * Class DomainsController
 * @package my\controllers
 */
class DomainsController extends CustomController
{
    public $activeTab = 'domains';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'guestAccess' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
                    ],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'except' => ['order-domain'],
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

                            if (!$customer || !$customer->can('domains')) {
                                $this->redirect('/');
                                Yii::$app->end();
                            }

                            return true;
                        }
                    ],
                ],
            ],
            'orderAccess' => [
                'class' => AccessControl::class,
                'only' => ['order-domain2', 'order2'], // FIXME 2
                'rules' => [
                    [
                        'allow' => false,
                        'matchCallback' => function () {
                            $this->redirect('/domains');
                            Yii::$app->end();
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'order' => ['POST', 'GET'],
                    'order-domain' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['order-domain']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['order-domain'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * View Domains list
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app', 'pages.title.domains');

        $domainsSearch = new DomainsSearch();
        $domainsSearch->setParams([
            'customer_id' => Yii::$app->user->identity->id
        ]);

        return $this->render('index', [
            'domains' => $domainsSearch->search(),
        ]);
    }

    /**
     * Create order
     * @return array|string|Response
     * @throws \Throwable
     */
    public function actionOrder()
    {
        /**
         * @var Auth $user
         */
        $user = Yii::$app->user->getIdentity();

        $this->view->title = Yii::t('app', 'pages.title.order_domain');

        $model = new OrderDomainForm();
        $model->setUser($user);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post())) {
                if (!$model->save()) {
                    return [
                        'status' => 'error',
                        'error' => ActiveForm::firstError($model)
                    ];
                }
                return [
                    'status' => 'success',
                    'redirect' => Url::to('/invoices/' . $model->code, true)
                ];
            }

            return [
                'status' => 'error',
                'error' => 'Invalid form data'
            ];
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/invoices/' . $model->code);
        }

        return $this->render('order', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $order
     * @return array
     * @throws \Throwable
     */
    public function actionOrderDomain($order)
    {
        $this->view->title = Yii::t('app', 'pages.title.order');

        switch ($order) {
            case 'store':
                $model = new OrderStoreForm();
                $model->setIp(Yii::$app->request->getUserIP());
                break;
            case 'panel':
                $model = new OrderPanelForm();
                break;
            default:
                return [
                    'status' => 'error',
                    'error' => Yii::t('app', 'domain.order.error_invalid_form_data')
                ];
        }

        $model->scenario = OrderStoreForm::SCENARIO_CREATE_DOMAIN;

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
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
            'error' => Yii::t('app', 'domain.order.error_invalid_form_data')
        ];
    }
}
