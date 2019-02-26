<?php

namespace sommerce\controllers;

use common\models\store\PageFiles;
use sommerce\helpers\PageFilesHelper;
use sommerce\helpers\PagesHelper;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Render page by url
     * @param string $url
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionIndex($url)
    {
        $page = PagesHelper::getPage($url);

        $this->pageTitle = $page['title'];
        $this->seoKeywords = $page['seo_keywords'];
        $this->seoDescription = $page['seo_description'];

        $content = $this->renderTwigContent($page['twig']);

        return $content;
    }

    /**
     * Render page styles by url
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionStyles()
    {
        $files = PageFilesHelper::getFileByName('styles.css');

        return Yii::$app->response->sendContentAsFile($files['content'], 'styles.css', [
            'mimeType' => 'text/css;charset=UTF-8',
            'inline' => true,
        ]);
    }

    /**
     * Render page scripts by url
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionScripts()
    {
        $files = PageFilesHelper::getFileByName('scripts.js');

        return Yii::$app->response->sendContentAsFile($files['content'], 'scripts.js', [
            'mimeType' => 'text/javascript;charset=UTF-8',
            'inline' => true,
        ]);
    }

}
