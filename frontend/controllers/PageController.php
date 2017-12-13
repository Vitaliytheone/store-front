<?php
namespace frontend\controllers;
use common\models\store\Pages;
use yii\web\NotFoundHttpException;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Displays page.
     * @param int $id
     * @return string
     */
    public function actionIndex($id)
    {
        $page = $this->_findPage($id);

        return $this->render($page->template, [
            'page' => $page
        ]);
    }

    /**
     * Find page or return exception
     * @param int $id
     * @return Pages
     * @throws NotFoundHttpException
     */
    protected function _findPage(int $id)
    {
        $product = Pages::find()->active()->andWhere([
            'id' => $id,
        ])->one();

        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }
}
