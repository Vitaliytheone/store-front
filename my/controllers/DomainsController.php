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


/**
 * Class DomainsController
 * @package my\controllers
 */
class DomainsController extends CustomController
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

                            if (!$customer || !$customer->can('domains')) {
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
     * @return string|\yii\web\Response
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
}
