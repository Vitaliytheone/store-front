<?php

namespace control_panel\controllers;

use common\models\sommerces\MyActivityLog;
use common\components\filters\DisableCsrfToken;
use control_panel\helpers\UserHelper;
use control_panel\models\forms\LoginFormSuper;
use Yii;
use common\models\sommerces\Customers;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Class SystemController
 * @package control_panel\controllers
 */
class SystemController extends CustomController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'token' => [
                'class' => DisableCsrfToken::class,
            ],
        ];
    }

    /**
     * System pip action
     */
    public function actionPpip()
    {
        $path = Yii::getAlias('@runtime/payments/');
        $output = $_SERVER['HTTP_HOST']."\n".date("Y-m-d H:i:s", time()+10803)."\n\n\n\n";
        $output .= json_encode(array("POST" => $_POST, "GET" => $_GET, "SERVER" => $_SERVER), JSON_PRETTY_PRINT)."\n\n\n";
        $fp = fopen($path."/ppipn.log", "a+");
        fputs ($fp, $output);
        fclose ($fp);
    }

    /**
     * System mail action
     */
    public function actionSysmail()
    {
        $post = Yii::$app->request->post();

        if (
            !empty($post['name']) &&
            !empty($post['secret']) &&
            !empty($post['email']) &&
            !empty($post['subject']) &&
            !empty($post['ip']) &&
            !empty($post['message'])
        ) {
            if ($post['secret'] == Yii::$app->params['sysmailSecret']) {
                Yii::$app->mailerSwift->compose(
                    ['html' => 'sysmail'],
                    $post
                )
                    ->setFrom(Yii::$app->params['swift.username'])
                    ->setTo(Yii::$app->params['sysmailSupportEmail'])
                    ->setReplyTo([$post['email'] => $post['name']])
                    ->setSubject('Perfect Panel contact form ' . $post['email'] . ' @' . date("Y-m-d H:i:s", time() + Yii::$app->params['time']))
                    ->send();

                echo 'ok';
            }
        } else {

        }
    }

    /**
     * System customer auth
     * @return Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionSuperadminauth()
    {
        if ($_GET['key'] == Yii::$app->params['gypAuth']) {
            $customer = Customers::findOne(['auth_token' => $_GET['token']]);
            if ($customer !== null) {
                Yii::$app->user->logout();
                $customer->auth_token = '';
                $customer->update();

                $model = new LoginFormSuper();
                $model->load(array('LoginFormSuper' => array('username' => $customer->email,'password' => $customer->password)));
                $model->login();

                MyActivityLog::log(MyActivityLog::E_SUPER_USER_AUTHORIZATION, $customer->id, $customer->id, UserHelper::getHash());

                return $this->redirect('/stores');
            }
        }

        return $this->redirect('/');
    }

    /**
     * Test succesed add ssl to ddos guard service
     * @return string
     * @throws HttpException
     */
    public function actionDdosSuccess()
    {
        return 'OK';
    }

    /**
     * Test exception add ssl to ddos guard service
     * @throws HttpException
     */
    public function actionDdosError()
    {
        throw new HttpException(500);
    }

    /**
     * Test successed dns
     * @return array
     * @throws HttpException
     */
    public function actionDns()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'status' => 'Success'
        ];
    }

    /**
     * Test dns list
     * @return array
     * @throws HttpException
     */
    public function actionDnsList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            [
                'id' => 1
            ],
            [
                'id' => 2
            ],
        ];
    }
}
