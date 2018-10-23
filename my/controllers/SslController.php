<?php

namespace my\controllers;

use common\models\panels\Customers;
use my\models\forms\OrderSslForm;
use my\models\search\SslSearch;
use Yii;

/**
 * Class SslController
 * @package my\controllers
 */
class SslController extends CustomController
{

    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Ssl page
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'SSL Certificates';

        $sslList = new SslSearch();
        $sslList->setParams(['customer_id' => Yii::$app->user->identity->id]);

        return $this->render('index', [
            'sslList' => $sslList->search(),
        ]);
    }

    /**
     * Ssl page
     * @return string
     */
    public function actionOrder()
    {
        $this->view->title = 'Order new certificate';

        $customer = Customers::findOne(Yii::$app->user->identity->id);

        $model = new OrderSslForm();
        $model->setCustomer($customer);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('/invoices/' . $model->code);
        }

        return $this->render('order', [
            'model' => $model
        ]);
    }
}