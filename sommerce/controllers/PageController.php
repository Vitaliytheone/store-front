<?php

namespace sommerce\controllers;

use common\models\sommerce\Payments;
use my\helpers\Url;
use sommerce\models\forms\PaymentsModalForm;
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

        return $this->renderTwigContent($content, [], false);
    }

    /**
     * Render page by url
     * @param string $url
     * @param string $hash
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionIndex($url = 'index', $hash = null)
    {
        $page = PagesHelper::getPage($url);

        if (!$page) {
            throw new NotFoundHttpException("Page by url '{$url}' not found");
        }

        if ($hash) {
            $payment = Payments::findOne(['hash' => $hash]);

            if (!$payment) {
                throw new NotFoundHttpException("Payment not found!");
            }

            $modal = new PaymentsModalForm();
            $modal->setStore($this->store);

            $this->addModule('paymentResultModal',  $modal->getModalData($payment));
        }

        Url::remember();

        $this->pageTitle = $page['seo_title'];
        $this->seoKeywords = $page['seo_keywords'];
        $this->seoDescription = $page['seo_description'];

        $content = $page['twig'] ?? '';

        $this->addModule('contactsForm', [
            'contact_action_url' => Url::toRoute(['/system/contacts']),
        ]);

        $orderForm = new OrderForm();
        $orderForm->setStore($this->store);

        $this->addModule('orderFormFrontend', [
            'order_data_url' => Url::toRoute(['/order/get-order-data', 'id' => '_id_']),
            'form_action_url' => Url::toRoute(['/order']),
            'form_validate_ulr' => Url::toRoute(['/order/validate']),
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
}
