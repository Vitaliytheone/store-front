<?php

namespace my\controllers;

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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
