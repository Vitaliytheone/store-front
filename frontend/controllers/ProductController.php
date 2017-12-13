<?php
namespace frontend\controllers;

use common\models\store\Products;
use Yii;
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

        $this->view->title = $product->name;

        return $this->render('product', [
            'product' => $product,
        ]);
    }

    /**
     * Find product or return exception
     * @param int $id
     * @return Products
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
