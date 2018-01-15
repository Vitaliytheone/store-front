<?php
namespace frontend\controllers;
use common\models\store\Pages;
use yii\web\NotFoundHttpException;
use Yii;
use frontend\models\forms\ContactForm;
use common\components\ActiveForm;


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

        switch ($page->url) {
            case $page->template === $page::TEMPLATE_CONTACT : return $this->_actionContactUs($page->template); break;
        }

        return $this->render($page->template . '.twig', [
            'page' => [
                'title' => $page->name,
                'content' => $page->content,
            ]
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

        if (
            $contactForm->load($request->post()) &&
            $contactForm->validate() &&
            $contactForm->contact()
        ) {
            return $this->refresh();
        }

        return $this->render($template . '.twig', [
            'data' => $contactForm,
            'error' => $contactForm->hasErrors(),
            'errorText' => ActiveForm::firstError($contactForm),
            'success' => $contactForm->getSentSuccess(),
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
        $page = Pages::find()->active()->andWhere([
            'id' => $id,
        ])->one();

        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $page;
    }
}
