<?php
namespace gateway\controllers;

use common\models\panels\Params;
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
        if (!$this->gateway->isInactive()) {
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
        $this->pageTitle = $this->gateway->seo_title;

        return $this->render('index.twig');
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
