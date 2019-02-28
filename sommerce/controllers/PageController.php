<?php

namespace sommerce\controllers;

use common\models\sommerce\Pages;
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
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionIndex($url)
    {
        $page = $this->_findPage($url);

        $content = $this->renderContentPartial($page->twig, [
            'site' => 100,
            'site2' => 100,
        ]);

        return $content;
    }

    /**
     * Render page styles by url
     * @param string $url
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionStyles($url)
    {
        $page = $this->_findPage($url);

        return Yii::$app->response->sendContentAsFile($page->styles, 'style.css', [
            'mimeType' => 'text/css;charset=UTF-8',
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
