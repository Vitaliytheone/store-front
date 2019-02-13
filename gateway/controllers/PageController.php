<?php
namespace gateway\controllers;

use common\models\gateway\Files;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Displays page.
     * @param int|array $id
     * @return string
     */
    public function actionIndex($id = ['url' => 'index'])
    {
        $file = $this->_findFile($id);

        $this->pageTitle = $file->getTitle();

        return $this->renderTwigContent($file->content);
    }

    /**
     * Find file or return exception
     * @param array|int $attributes
     * @return Files
     * @throws NotFoundHttpException
     */
    protected function _findFile($attributes)
    {
        if (is_int($attributes)) {
            $attributes = [
                'id' => $attributes
            ];
        }

        $file = Files::find()->active()->andWhere($attributes)->one();

        if (!$file) {
            throw new NotFoundHttpException();
        }

        return $file;
    }
}
