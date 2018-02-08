<?php
namespace frontend\controllers;

use common\models\stores\Stores;
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
        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();
        $this->pageTitle = $store->seo_title;

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
