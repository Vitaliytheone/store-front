<?php
namespace gateway\controllers;

use common\components\filters\DisableCsrfToken;
use common\models\gateway\Payments;
use gateway\models\forms\CheckoutForm;
use payments\Payment;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Payments controller
 */
class PaymentsController extends CommonController
{
    public $enableCsrfValidation = false;

    public $enableDomainValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'checkout' => ['POST'],
                ],
            ],
            'token' => [
                'class' => DisableCsrfToken::class,
                'only' => ['checkout', 'processing',],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionCheckout()
    {
        $model = new CheckoutForm();
        $model->setGateway(Yii::$app->gateway->getInstance());
        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            if ($model->redirect) {
                return $this->redirect($model->redirect);
            } else if (!empty($model->formData)) {
                return $this->renderPartial('checkout.php', $model->formData);
            }
        }

        return Yii::t('app', 'checkout.error');
    }

    /**
     * Process payment method
     * @param $method
     * @return string|\yii\web\Response
     */
    public function actionProcessing($method)
    {
        // if ($_SERVER['REMOTE_ADDR'] == '') {
        //     $json = json_decode('', 1);
        //     $_POST = ArrayHelper::getValue($json, 'POST', []);
        //     $_GET = ArrayHelper::getValue($json, 'GET', []);
        //     $_SERVER = ArrayHelper::getValue($json, 'SERVER', []);
        // }

        $paymentMethod = Payment::getPayment($method);
        $paymentMethod->setGateway(Yii::$app->gateway->getInstance());
        $result = $paymentMethod->process();

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

        $payment = !empty($result['payment_id']) ? Payments::findOne($result['payment_id']) : null;

        if ($payment && !empty($payment->return_url)) {
            return $this->redirect($payment->return_url);
        }
        
        return '';
    }
}
