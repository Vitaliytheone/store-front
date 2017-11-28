<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use frontend\modules\admin\models\search\PaymentsSearch;
use frontend\modules\admin\models\forms\GetPaymentDetailsForm;

/**
 * Class PaymentsController
 * @package frontend\modules\admin\controllers\
 */
class PaymentsController extends CustomController
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

        $model = GetPaymentDetailsForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $details = $model->details();

        if (!$details) {
            throw new NotFoundHttpException();
        }

        return ['details' => $details];
    }
}
