<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\Customers;
use my\modules\superadmin\models\forms\CustomerPasswordForm;
use my\modules\superadmin\models\forms\EditCustomerForm;
use my\modules\superadmin\models\search\CustomersSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AjaxFilter;
use my\components\SuperAccessControl;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;

/**
 * CustomersController for the `superadmin` module
 */
class CustomersController extends CustomController
{
    public $activeTab = 'customers';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'activate-stores'=> ['POST'],
                    'change-status'=> ['POST'],
                    'edit' => ['POST'],
                    'set-password' => ['POST'],
                    'activate-domain' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['ajax-customers', 'edit', 'set-password']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['edit', 'set-password', 'ajax-customers'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.customers');

        $customersSearch = new CustomersSearch();
        $customersSearch->setParams(Yii::$app->request->get());

        $filters = $customersSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'customers' => $customersSearch->search(),
            'navs' => $customersSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $customersSearch->getParams(),
        ]);
    }

    /**
     * Change order status
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus()
    {
        $params = Yii::$app->request;
        $id = $params->post('id');
        $status = $params->post('status');
        $order = $this->findModel($id);

        $order->changeStatus($status);

        return $this->redirect(Url::toRoute('/customers'));
    }

    /**
     * Edit customer data.
     *
     * @access public
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $customer = $this->findModel($id);

        $model = new EditCustomerForm();
        $model->setCustomer($customer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }

    /**
     * Change customer password action
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSetPassword($id)
    {
        $customer = $this->findModel($id);

        $model = new CustomerPasswordForm();
        $model->setCustomer($customer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }

    /**
     * Auth uses customer
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function actionAuth($id)
    {
        $customer = $this->findModel($id);

        $customer->generateAuthToken();
        $customer->save(false);

        $url = Yii::$app->params['my_domain'] . '/authSuperadmin/' . Yii::$app->params['gypAuth'] . '/' . $customer->auth_token;
        $this->redirect(Url::to('//' . $url, true));
    }

    /**
     * Activate stores feature
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionActivateStores()
    {
        $request = Yii::$app->request;

        $customer = $this->findModel($request->post('id'));

        $customer->activateStores();

        return $this->redirect(Url::toRoute('/customers'));
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionActivateDomain()
    {
        $request = Yii::$app->request;

        $customer = $this->findModel($request->post('id'));

        $customer->activateDomains();

        return $this->redirect(Url::toRoute('/customers'));
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionAjaxCustomers()
    {
        $params = Yii::$app->request->get();
        $params['status'] = isset($params['status']) ? $params['status'] : Customers::STATUS_ACTIVE;
        return CustomersSearch::ajaxSelectSearch($params['email'], $params['status']);
    }

    /**
     * Find customer model
     * @param $id
     * @return null|Customers
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Customers::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
