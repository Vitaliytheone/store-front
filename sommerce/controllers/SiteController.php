<?php

namespace sommerce\controllers;

use common\components\ActiveForm;
use common\models\sommerces\Params;
use common\models\sommerces\SslValidation;
use sommerce\models\forms\ContactForm;
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

    /**
     * Send `contact form` email
     * @return string
     */
    public function actionContactUs()
    {
        $request = Yii::$app->getRequest();
        $contactForm = new ContactForm();

        if ($contactForm->load($request->post()) && $contactForm->contact()) {
            return json_encode([
                'error' => $contactForm->hasErrors(),
                'success' => $contactForm->getSentSuccess(),
            ]);
        } else {
            return json_encode([
                'error' => $contactForm->hasErrors(),
                'error_message' => ActiveForm::firstError($contactForm),
            ]);
        }
    }

}
