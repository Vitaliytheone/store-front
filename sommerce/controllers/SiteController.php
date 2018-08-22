<?php
namespace sommerce\controllers;

use common\models\panels\SslValidation;
use sommerce\models\search\BlocksSearch;
use Yii;
use yii\web\NotFoundHttpException;

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

        return $this->renderPartialCustom('404.twig');
    }

    /**
     * Frozen action
     * @return string
     */
    public function actionFrozen()
    {

        if (!$this->store->isInactive()) {
            return $this->redirect('/');
        }

        return $this->renderPartial('frozen');
    }

    /**
     * Displays index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->pageTitle = $this->store->seo_title;

        return $this->render('index.twig', [
            'block' => BlocksSearch::search($this->store)
        ]);
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


    /**
     * Validate ssl certificate. For robot comings
     * @param $filename
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSsl($filename)
    {
        $model = SslValidation::findOne([
            'pid' => $this->store->id,
            'file_name' => $filename . '.txt'
        ]);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model->content;
    }
}
