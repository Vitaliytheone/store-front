<?php
namespace frontend\controllers;

/**
 * Site controller
 */
class SiteController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => '@frontend/views/site/404.php'
            ],
        ];
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
        return $this->render('checkout');
    }
}
