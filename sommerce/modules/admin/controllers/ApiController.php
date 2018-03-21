<?php

namespace sommerce\modules\admin\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use sommerce\modules\admin\models\search\UrlsSearch;

class ApiController extends Controller
{
    /** @var array List of ajax action ids */
    private static $_ajaxActions = [
        'get-url-list',
    ];

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

}