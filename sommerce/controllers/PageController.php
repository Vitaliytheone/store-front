<?php

namespace sommerce\controllers;

use my\helpers\Url;
use sommerce\helpers\PageFilesHelper;
use sommerce\helpers\PagesHelper;
use sommerce\models\forms\OrderForm;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Error action
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionError()
    {
        $content = file_get_contents(self::getTwigView('404'));

        // TODO:: REMOVE THIS DEBUG!!!!
//        error_log(print_r(Yii::$app->errorHandler->exception,1));

        return $this->renderTwigContent($content, [], false);
    }

    /**
     * Render page by url
     * @param string $url
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionIndex($url = 'index')
    {
        $page = PagesHelper::getPage($url);

        if (!$page) {
            throw new NotFoundHttpException("Page by url '{$url}' not found");
        }

        Url::remember();

        $this->pageTitle = $page['seo_title'];
        $this->seoKeywords = $page['seo_keywords'];
        $this->seoDescription = $page['seo_description'];

        $content = $page['twig'] ?? '';

        $orderForm = new OrderForm();
        $orderForm->setStore($this->store);

        $this->addModule('orderFormFrontend', [
            'order_data_url' => Url::toRoute(['/cart/get-order-data', 'id' => '_id_']),
            'form_action_url' => Url::toRoute(['/cart']),
            'form_validate_ulr' => Url::toRoute(['/cart/validate']),
            'payment_methods' =>  $orderForm->getPaymentsMethodsForView(),
            'fieldOptions' => $orderForm->getPaymentsFields(),
            'options' => $orderForm->getJsOptions(),
        ]);

        return $this->renderTwigContent($content);
    }

    /**
     * Render page styles by url
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionStyles($name = 'styles.css')
    {
        $files = PageFilesHelper::getFileByName($name);

        if (empty($files)) {
            throw new NotFoundHttpException("File {$name} not found");
        }

        return Yii::$app->response->sendContentAsFile($files['content'], $name, [
            'mimeType' => 'text/css;charset=UTF-8',
            'inline' => true,
        ]);
    }

    /**
     * Render page scripts by url
     * @param string $name
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionScripts($name = 'scripts.js')
    {
        $files = PageFilesHelper::getFileByName($name);

        if (empty($files)) {
            throw new NotFoundHttpException("File {$name} not found");
        }

        return Yii::$app->response->sendContentAsFile($files['content'], $name, [
            'mimeType' => 'text/javascript;charset=UTF-8',
            'inline' => true,
        ]);
    }

    /**
     * Add payment modal
     */
    protected function addPaymentModal()
    {
        $cookies = Yii::$app->request->cookies;
        if (($cookie = $cookies->get('modal')) !== null) {
            $this->addModule('paymentResultModal', $cookie->value);
            $cookies = Yii::$app->response->cookies;
            $cookies->remove('modal');
        }
    }
}
