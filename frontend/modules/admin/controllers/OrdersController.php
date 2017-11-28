<?php

namespace frontend\modules\admin\controllers;

use frontend\helpers\UiHelper;
use Yii;
use yii\filters\AccessControl;
use frontend\modules\admin\components\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use frontend\modules\admin\models\search\OrdersSearch;
use frontend\modules\admin\models\forms\CancelSuborderForm;
use frontend\modules\admin\models\forms\ChangeSuborderStatusForm;
use frontend\modules\admin\models\forms\GetSuborderDetailsForm;
use frontend\modules\admin\models\forms\ResendSuborderForm;

/**
 * Class OrdersController
 * @package frontend\modules\admin\controllers
 */
class OrdersController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->addModule('ordersDetails');
        $this->addModule('ordersClipboard');

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

        $model = GetSuborderDetailsForm::findOne($suborder_id);

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
     * @param $id
     * @param $status
     * @throws Yii\web\BadRequestHttpException
     * @throws Yii\web\NotFoundHttpException
     * @throws Yii\web\NotAcceptableHttpException
     */
    public function actionChangeStatus($id, $status)
    {
        $model = ChangeSuborderStatusForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->changeStatus($status)) {
            throw new NotAcceptableHttpException();
        }

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
        $model = CancelSuborderForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->cancel()) {
            throw new NotAcceptableHttpException();
        }

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
        $model = ResendSuborderForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->resend()) {
            throw new NotAcceptableHttpException();
        }

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