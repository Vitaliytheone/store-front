<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use frontend\modules\admin\models\search\UrlsSearch;

class ApiController extends Controller
{
    /** @var array List of ajax action ids */
    private static $_ajaxActions = [
        'get-url-list',
    ];

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
     * Setup default ajax actions params
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();

        // Set Ajax params
        if (in_array($action->id, static::$_ajaxActions)) {

            if (!$request->isAjax) {
                exit;
            }

            $response->format = Response::FORMAT_JSON;
        }

        return parent::beforeAction($action);
    }

    /**
     * Return `products` and `packages` available union urls list
     * @return array
     */
    public function actionGetUrlList()
    {
        $urlsModel = new UrlsSearch();
        $urls = $urlsModel->searchUrls();

        return $urls;
    }

    /**
     * TODO:: ONLY FOR TEST Twocheckout purpose! Delete after test!!!
     */
    public function actionTwocheckoutTest()
    {
        $request = Yii::$app->getRequest();
        $get = $request->get();
        $post = $request->post();

        error_log(print_r($get, 1),0);
        error_log(print_r($post, 1),0);

        return true;
    }

}