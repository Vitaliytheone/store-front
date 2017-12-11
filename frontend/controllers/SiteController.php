<?php
namespace frontend\controllers;

use common\models\store\Products;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use frontend\models\forms\ProductViewForm;

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
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProduct($id)
    {
        Url::remember();

        $product = $this->_findProduct($id);

        $this->view->title = $product->name;

        return $this->render('product', [
            'product' => $product,
        ]);
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
     * Find product or return exception
     * @param int $id
     * @return Products
     * @throws NotFoundHttpException
     */
    protected function _findProduct(int $id)
    {
        $product = ProductViewForm::findOne([
            'id' => $id,
            'visibility' => ProductViewForm::VISIBILITY_YES,
        ]);

        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }
}
