<?php
namespace gateway\controllers;

use Yii;

/**
 * Site controller
 */
class SiteController extends CustomController
{
    /**
     * Error action
     * @return string
     */
    public function actionError()
    {
        $this->view->title = Yii::t('app', '404.title');

        return $this->renderPartialCustom('404.twig');
    }

    /**
     * Frozen action
     * @return string
     */
    public function actionFrozen()
    {
        if (!$this->gateway->isInactive()) {
            return $this->redirect('/');
        }

        return $this->renderPartial('frozen');
    }

    /**
     * Displays index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->pageTitle = $this->gateway->seo_title;

        return $this->render('index.twig');
    }
}
