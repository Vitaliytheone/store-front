<?php
namespace frontend\controllers;

use Yii;

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
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays product page.
     *
     * @return string
     */
    public function actionProduct()
    {
        return $this->render('product');
    }

    /**
     * Displays cart page.
     *
     * @return string
     */
    public function actionCart()
    {
        return $this->render('cart');
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

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        return $this->render('contact');
    }

    /**
     * Displays content page.
     *
     * @return string
     */
    public function actionContent()
    {
        return $this->render('content');
    }

    /**
     * Displays cart page.
     *
     * @return string
     */
    public function actionAddToCart()
    {
        return $this->render('add_to_cart');
    }
}
