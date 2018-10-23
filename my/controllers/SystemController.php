<?php

namespace my\controllers;

use common\models\panels\MyActivityLog;
use my\helpers\UserHelper;
use my\mail\mailers\PanelFrozen;
use my\models\forms\LoginFormSuper;
use common\models\panels\Project;
use Yii;
use common\models\panels\Customers;
use yii\web\HttpException;
use yii\web\Response;
use yii\filters\ContentNegotiator;

/**
 * Class SystemController
 * @package my\controllers
 */
class SystemController extends CustomController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'content' => [
                    'class' => ContentNegotiator::class,
                    'only' => ['dns', 'dns-list'],
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                    ],
                ],
            ]
        );
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

                return $this->redirect('/panels');
            }
        }

        return $this->redirect('/');
    }

    /**
     * Test succesed add ssl to ddos guard service
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
     * @throws HttpException
     */
    public function actionDns()
    {
        return [
            'status' => 'Success'
        ];
    }

    /**
     * Test dns list
     * @throws HttpException
     */
    public function actionDnsList()
    {
        return [
            [
                'id' => 1
            ],
            [
                'id' => 2
            ],
        ];
    }

    /**
     * @param $key
     * @param $id
     * @return string|void
     */
    public function actionPanelNotify($key, $id)
    {
        if (Yii::$app->params['gypAuth'] !== $key) {
            return;
        }

        $project = Project::findOne([
            'id' => $id,
            'act' => Project::STATUS_FROZEN
        ]);

        if (!$project) {
            return;
        }

        $mail = new PanelFrozen([
            'project' => $project
        ]);
        $mail->send();

        return 'OK';
    }
}
