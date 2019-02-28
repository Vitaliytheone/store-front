<?php

namespace store\modules\admin\controllers;

use store\events\Events;
use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use store\helpers\UiHelper;
use Yii;
use store\modules\admin\components\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use store\modules\admin\models\search\OrdersSearch;
use store\modules\admin\models\SuborderDetails;
use store\modules\admin\models\forms\SuborderForm;

/**
 * Class OrdersController
 * @package store\modules\admin\controllers
 */
class OrdersController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->addModule('ordersDetails');
        $this->addModule('ordersModalAlerts');
        $this->addModule('ordersClipboard', [
            'messageCopied' => Yii::t('admin', 'orders.message_copied'),
        ]);

        return parent::beforeAction($action);
    }

    /**
     * Render found & filtered orders list
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('admin', 'orders.page_title');

        $searchModel = new OrdersSearch();
        $searchModel->setStore($this->store);
        $ordersDataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'ordersDataProvider' => $ordersDataProvider,
            'ordersSearchModel' => $searchModel
        ]);
    }

    /**
     * Return Order details data AJAX action
     * GET /admin/orders/get-order-details?suborder_id={{suborderId}}
     * @param $suborder_id
     * @throws Yii\web\BadRequestHttpException
     * @throws Yii\web\NotFoundHttpException
     * @return array
     */
    public function actionGetOrderDetails($suborder_id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            throw new BadRequestHttpException();
        }

        $model = SuborderDetails::findOne($suborder_id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $details = $model->details();

        if (!$details) {
            throw new NotFoundHttpException();
        }

        return ['details' => $details];
    }

    /**
     * Suborder `change status`  action
     * Accepted only allowed new statuses.
     * @param integer $id
     * @param integer $status
     * @throws Yii\web\BadRequestHttpException
     * @throws Yii\web\NotFoundHttpException
     * @throws Yii\web\NotAcceptableHttpException
     */
    public function actionChangeStatus($id, $status)
    {
        /**
         * @var Stores $store
         */
        $model = SuborderForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->changeStatus($status)) {
            $this->redirect(Url::toRoute(["/orders"]));
        }

        Events::add(Events::EVENT_STORE_ORDER_CHANGED_STATUS, [
            'suborderId' => $id,
            'storeId' => $this->store->id,
            'status' => $status
        ]);

        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_ORDERS_ORDER_STATUS_CHANGED, $model->id, $model->id);

        UiHelper::message(Yii::t('admin', 'orders.message_status_changed'));

        $filters = static::_queryParams();
        $this->redirect(Url::toRoute(["/orders$filters"]));
    }

    /**
     * Suborder `cancel` action
     * @param $id
     * @throws Yii\web\NotFoundHttpException
     * @throws Yii\web\NotAcceptableHttpException
     */
    public function actionCancel($id)
    {
        $model = SuborderForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->cancel()) {
            $this->redirect(Url::toRoute(["/orders"]));
        }

        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_ORDERS_ORDER_CANCELED, $model->id, $model->id);

        UiHelper::message(Yii::t('admin', 'orders.message_status_changed'));

        $filters = static::_queryParams();
        $this->redirect(Url::toRoute(["/orders$filters"]));
    }

    /**
     * Suborder `resend` action
     * @param $id
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionResend($id)
    {
        $model = SuborderForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->setStore($this->store);

        if (!$model->resend()) {
            $this->redirect(Url::toRoute(["/orders"]));
        }

        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_ORDERS_ORDER_RESENT, $model->id, $model->id);

        UiHelper::message(Yii::t('admin', 'orders.message_status_changed'));

        $filters = static::_queryParams();
        $this->redirect(Url::toRoute(["/orders$filters"]));
    }

    /**
     * Return current filters array as redirect query params
     * @return null|string
     */
    private static function _queryParams()
    {
        $filters = yii::$app->getRequest()->get('filters');

        return is_array($filters) ? ( '?' . http_build_query($filters)) : null;
    }

}