<?php

namespace sommerce\controllers;

use common\models\panels\Params;
use common\models\panels\SslValidation;
use common\models\store\Pages;
use sommerce\models\search\BlocksSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends CustomController
{
    /**
     * Error action
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionError()
    {
        $this->view->title = Yii::t('app', '404.title');

        $page = Pages::find()->active()->andWhere([
            'url' => '404',
        ])->one();

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $this->renderContentPartial($page->twig, ['title' => $this->view->title, 'code' => 404]);
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
        $method = ArrayHelper::getValue(explode('/', mb_strtolower(Yii::$app->request->url)), 2);

        switch ($method) {
            case 'pki-validation':

                $model = SslValidation::findOne([
                    'pid' => $this->store->id,
                    'file_name' => $filename . '.txt'
                ]);

                if (!$model) {
                    throw new NotFoundHttpException();
                }

                $content = $model->content;

                break;

            case 'acme-challenge':

                Yii::$app->response->format = Response::FORMAT_RAW;
                Yii::$app->response->headers->add('Content-Type', 'text/plain; charset=utf-8');

                $accountThumbPrint = Params::get(Params::CATEGORY_SERVICE, Params::CODE_LETSENCRYPT, 'account_thumbprint');

                if (!$accountThumbPrint) {
                    throw new NotFoundHttpException();
                }

                $content = $filename . '.' . $accountThumbPrint;

                break;

            default:
                throw new NotFoundHttpException();
        }

        return $content;
    }
}
