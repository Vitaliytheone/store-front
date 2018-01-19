<?php
namespace frontend\controllers;

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

        return $this->renderPartial('404');
    }

    /**
     * Displays index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index.twig');
    }

    /**
     * Displays checkout page.
     *
     * @return string
     */
    public function actionCheckout()
    {
        return $this->renderPartial('checkout');
    }
}
