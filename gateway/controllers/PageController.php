<?php
namespace gateway\controllers;

use common\models\gateway\Pages;
use common\models\gateway\ThemesFiles;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Displays page.
     * @param int|array $id
     * @return string
     */
    public function actionIndex($id = ['url' => 'index'])
    {
        $page = $this->_findPage($id);
        $this->pageTitle = $page->seo_title;
        $this->seoDescription = $page->seo_description;
        $this->seoKeywords = $page->seo_keywords;

        return $this->renderContent($page->getThemeTemplate(), [
            'page' => [
                'title' => $page->title,
                'content' => $page->content,
            ]
        ], true);
    }

    /**
     * Find page or return exception
     * @param array|int $attributes
     * @return Pages
     * @throws NotFoundHttpException
     */
    protected function _findPage($attributes)
    {
        if (is_int($attributes)) {
            $attributes = [
                'id' => $attributes
            ];
        }

        $page = Pages::find()->active()->andWhere($attributes)->one();

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $page;
    }
}
