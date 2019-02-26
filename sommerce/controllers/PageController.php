<?php

namespace sommerce\controllers;

use common\models\store\Pages;
use yii\web\NotFoundHttpException;
use Yii;
use sommerce\models\forms\ContactForm;
use common\components\ActiveForm;

/**
 * Page controller
 */
class PageController extends CustomController
{
    /**
     * Displays page.
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionIndex($id)
    {
        $page = $this->_findPage($id);
        $this->pageTitle = $page->seo_title;
        $this->seoDescription = $page->seo_description;
        $this->seoKeywords = $page->seo_keywords;

        switch ($page->url) {
            case $page->template === $page::TEMPLATE_CONTACT : return $this->_actionContactUs($page); break;
        }

        if ($page->template == Pages::TEMPLATE_FILE) {
            $this->layout = false;
            return $this->renderContent($page->content);
        }

        return $this->render($page->template . '.twig', [
            'page' => [
                'title' => $page->title,
                'content' => $page->content,
            ]
        ]);
    }

    /**
     * Render `contact form` page
     * @param Pages $page
     * @return string|\yii\web\Response
     */
    protected function _actionContactUs($page)
    {
        $request = Yii::$app->getRequest();
        $contactForm = new ContactForm();

        if ($contactForm->load($request->post()) && $contactForm->contact()) {
            return $this->refresh();
        }

        return $this->render($page->template . '.twig', [
            'page' => [
                'title' => $page->title,
                'content' => $page->content,
            ],
            'contact' => [
                'form' => [
                    'subject' => $contactForm->subject,
                    'name' => $contactForm->name,
                    'email' => $contactForm->email,
                    'message' => $contactForm->message,
                ],
            ],

            'error' => $contactForm->hasErrors(),
            'error_message' => ActiveForm::firstError($contactForm),
            'success' => $contactForm->getSentSuccess(),
            'captcha_key' => Yii::$app->params['reCaptcha.siteKey'],
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
        $page = Pages::find()->active()->andWhere([
            'id' => $id,
        ])->one();

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $page;
    }
}
