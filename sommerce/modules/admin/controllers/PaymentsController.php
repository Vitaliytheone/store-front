<?php

namespace sommerce\modules\admin\controllers;

use common\events\Events;
use common\models\store\Orders;
use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use sommerce\modules\admin\models\search\PaymentsSearch;
use sommerce\modules\admin\models\PaymentDetails;

/**
 * Class PaymentsController
 * @package sommerce\modules\admin\controllers\
 */
class PaymentsController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->addModule('payments');

        return parent::beforeAction($action);
    }

    /**
     * Render payments list
     * @return string
     */
    public function actionIndex()
    {
        // Event confirm
        Events::add(Events::EVENT_STORE_ORDER_CONFIRM, [
            'order' => Orders::findOne(166),
            'store' => Yii::$app->store->getInstance()
        ]);
        exit();
        $this->view->title = Yii::t('admin', 'payments.page_title');

        $searchModel = new PaymentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Return formatted payment details
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetDetails($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
           exit;
        }

        $model = PaymentDetails::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $details = $model->details();

        if (!$details) {
            throw new NotFoundHttpException();
        }

        return $details;
    }
}
