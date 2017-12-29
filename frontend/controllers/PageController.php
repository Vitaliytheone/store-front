<?php
namespace frontend\controllers;
use common\models\store\Pages;
use yii\web\NotFoundHttpException;
use Yii;
use yii\helpers\Url;
use frontend\models\forms\ContactForm;
use common\components\ActiveForm;
use frontend\helpers\UiHelper;


/**
 * Page controller
 */
class PageController extends CustomController
{
    const PAGE_CONTACT_US = 'contact-us';

    /**
     * Displays page.
     * @param int $id
     * @return string
     */
    public function actionIndex($id)
    {
        $page = $this->_findPage($id);

        switch ($page->url) {
            case self::PAGE_CONTACT_US : return $this->_actionContactUs($page->template); break;
        }

        return $this->render($page->template . '.twig', [
            'page' => $page
        ]);
    }

    /**
     * Render `contact form` page
     * @param $template
     * @return string|\yii\web\Response
     */
    protected function _actionContactUs($template)
    {
        $request = Yii::$app->getRequest();
        $contactForm = new ContactForm();

        if ($contactForm->contact($request->post())) {
            // UiHelper::message(Yii::t('store', 'Email was successfully sent!')); TODO:: Uncommit after update translation file
            return $this->redirect(Url::toRoute('/'));
        }

        return $this->render($template . '.twig', [
            'data' => $request->post('ContactForm'),
            'error' => $contactForm->hasErrors(),
            'errorText' => ActiveForm::firstError($contactForm),
            'reCaptchaSiteKey' => Yii::$app->params['reCaptcha.siteKey'],
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
