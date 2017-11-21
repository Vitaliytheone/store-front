<?php

namespace frontend\controllers;

use frontend\components\payments\Payment;
use Yii;
use common\models\stores\Stores;
use yii\helpers\ArrayHelper;


/**
 * Class PaymentsController
 * @package frontend\controllers
 */
class PaymentsController extends CustomController
{
    public $enableCsrfValidation = false;

    /**
     * Process payment method
     * @param $method
     */
    public function actionResult($method)
    {
        /**
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        // if ($_SERVER['REMOTE_ADDR'] == '') {
        //     $json = json_decode('', 1);
        //     $_POST = ArrayHelper::getValue($json, 'POST', []);
        //     $_GET = ArrayHelper::getValue($json, 'GET', []);
        //     $_SERVER = ArrayHelper::getValue($json, 'SERVER', []);
        // }

        $paymentMethod = Payment::getPayment($method);
        $result = $paymentMethod->process($store);

        if (!empty($result['content']) && !$paymentMethod->redirectProcessing) {

            // Если успешная ошлата и есть контент - выводим его
            if (1 == ArrayHelper::getValue($result, 'result')) {
                return $result['content'];
            }

            // Показываем ошибки если включен режим отображения их, если нет то пустая страница
            if ($paymentMethod->showErrors) {
                return $result['content'];
            } else {
                return '';
            }
        }

        return $this->redirect('/cart');
    }
}