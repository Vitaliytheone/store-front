<?php

namespace store\controllers;

use common\models\store\Products;
use store\models\search\ProductSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use store\models\forms\ProductViewForm;

/**
 * Product controller
 */
class ProductController extends CustomController
{
    /**
     * Displays product page.
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($id)
    {
        Url::remember();

        $product = $this->_findProduct($id);
        $this->pageTitle = $product->seo_title;
        $this->seoDescription = $product->seo_description;
        $this->seoKeywords = $product->seo_keywords;
        $this->view->title = $product->name;
        $data = (new ProductSearch($product))->search();

        return $this->render('product.twig', [
            'product' => [
                'id' => $product->id,
                'title' => Html::encode($product->name),
                'content' => $product->description,
                'color' => Html::encode($product->color),
            ] + $data
        ]);
    }

    /**
     * Find product or return exception
     * @param int $id
     * @return ProductViewForm|Products
     * @throws NotFoundHttpException
     */
    protected function _findProduct(int $id)
    {
        $product = ProductViewForm::find()->active()->andWhere([
            'id' => $id,
        ])->one();

        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }
}
