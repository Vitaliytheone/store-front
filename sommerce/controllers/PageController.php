<?php

namespace sommerce\controllers;

use common\models\store\Pages;
use sommerce\helpers\PageFilesHelper;
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
        $page = $this->_findPage($url);

        $content = $this->renderContent($page->twig);

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
        $files = PageFilesHelper::getFileByName('css', 'styles.css');

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
        $files = PageFilesHelper::getFileByName('js', 'scripts.js');

        return Yii::$app->response->sendContentAsFile($files['content'], 'scripts.js', [
            'mimeType' => 'text/javascript;charset=UTF-8',
            'inline' => true,
        ]);
    }

    /**
     * Find page or return exception
     * @param string $url
     * @return Pages
     * @throws NotFoundHttpException
     */
    protected function _findPage(string $url)
    {
        $page = Pages::find()->active()->andWhere([
            'url' => $url,
        ])->one();

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $page;
    }
}
