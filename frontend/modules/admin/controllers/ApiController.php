<?php

namespace frontend\modules\admin\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use frontend\modules\admin\models\search\UrlsSearch;

use common\components\panelchecker\PanelcheckerComponent;

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
     * TODO:: Only for test purpose
     * Levochecker test action
     * @return int
     */
    public function actionLevochecker()
    {
        $config = [
            'class' => PanelcheckerComponent::className(),
            'apiKey' => 'b9f1d6f809b793321c700f45ca382f59ef83bf644c48118e6d3b9902ab0cb86f',
            'apiVersion' => '1.0',
            'db' => [
                'name' => 'store',
                'table' => 'test',
            ],
            'dbFields' => [
                'domains_column' => 'domain',
                'status_column' => 'status',
                'updated_column' => 'updated_at',
                'created_column' => 'created_at',
                'ip_column' => 'server_ip',
                'details_column' => 'details',
            ],
            'proxy' => [
                'ip',
                'port',
                'type' => CURLPROXY_HTTP,
            ],
        ];

        /** @var \common\components\panelchecker\PanelcheckerComponent $checker */
        $checker = Yii::createObject($config);
        $res = $checker->check();

        error_log(print_r($res, 1),0);

        return true;
    }

}