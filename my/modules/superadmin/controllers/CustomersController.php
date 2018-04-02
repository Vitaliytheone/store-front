<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use my\helpers\UserHelper;
use common\models\panels\Customers;
use common\models\panels\MyActivityLog;
use my\modules\superadmin\models\forms\CustomerPasswordForm;
use my\modules\superadmin\models\forms\EditCustomerForm;
use my\modules\superadmin\models\search\CustomersSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CustomersController for the `superadmin` module
 */
class CustomersController extends CustomController
{
    public $activeTab = 'customers';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Customers';

        $customersSearch = new CustomersSearch();
        $customersSearch->setParams(Yii::$app->request->get());

        $filters = $customersSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'customers' => $customersSearch->search(),
            'navs' => $customersSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $customersSearch->getParams()
        ]);
    }

    /**
     * Change order status
     * @param int $id
     * @param int $status
     */
    public function actionChangeStatus($id, $status)
    {
        $order = $this->findModel($id);

        $order->changeStatus($status);

        return $this->redirect(Url::toRoute('/customers'));
    }

    /**
     * Edit customer data.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEdit($id)
    {
        $customer = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     */
    public function actionSetPassword($id)
    {
        $customer = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * @return array
     */
    public function actionAuth($id)
    {
        $customer = $this->findModel($id);

        $customer->generateAuthToken();
        $customer->save(false);

        MyActivityLog::log(MyActivityLog::E_SUPER_USER_AUTHORIZATION, $customer->id, $customer->id, UserHelper::getHash());

        $url = Yii::$app->params['my_domain'] . '/authSuperadmin/' . Yii::$app->params['gypAuth'] . '/' . $customer->auth_token;
        $this->redirect(Url::to('//' . $url, true));
    }

    /**
     * Enable referral
     * @param int $id
     */
    public function actionEnableReferral($id)
    {
        $customer = $this->findModel($id);

        if ($customer->can('enable_referral')) {
            $customer->referral_status = Customers::REFERRAL_ACTIVE;
            $customer->save(false);
        }

        return $this->redirect(Url::toRoute('/customers'));
    }

    /**
     * Disable referral
     * @param int $id
     */
    public function actionDisableReferral($id)
    {
        $customer = $this->findModel($id);

        if ($customer->can('disable_referral')) {
            $customer->referral_status = Customers::REFERRAL_NOT_ACTIVE;
            $customer->save(false);
        }

        return $this->redirect(Url::toRoute('/customers'));
    }

    /**
     * Activate stores feature
     * @param $id
     * @return Response
     */
    public function actionActivateStores($id)
    {
        $customer = $this->findModel($id);

        $customer->activateStores();

        return $this->redirect(Url::toRoute('/customers'));
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
