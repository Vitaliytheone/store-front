<?php
namespace frontend\controllers;

use frontend\helpers\UiHelper;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use frontend\models\forms\ProductViewForm;

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

        return $this->render('product.twig', [
            'product' => [
                'id' => $product->id,
                'title' => Html::encode($product->name),
                'content' => $product->description,
                'packages' => array_map(function ($package) {
                    return [
                        'id' => $package->id,
                        'best' => $package->best,
                        'quantity' => $package->quantity,
                        'name' => Html::encode($package->name),
                        'price' => $package->price,
                        'button' => [
                            'url_buy_now' => Url::toRoute("/order/$package->id"),
                        ],
                    ];
                }, $product->packages),
                'properties' => array_map(function ($property) {
                    return Html::encode($property);
                }, $product->properties),
            ],
        ]);
    }

    /**
     * Find product or return exception
     * @param int $id
     * @return ProductViewForm
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
