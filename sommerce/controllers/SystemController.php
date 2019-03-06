<?php

namespace sommerce\controllers;

use common\components\exceptions\FirstValidationErrorHttpException;
use common\components\response\CustomResponse;
use sommerce\models\forms\ContactForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * System controller
 */
class SystemController extends CustomController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['contacts']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'contacts' => ['POST'],
                ],
            ],
            'ajaxApi' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'contacts',
                ],
                'formats' => [
                    'application/json' => CustomResponse::FORMAT_AJAX_API,
                ],
            ],
        ]);
    }


    /**
     * Send `contact form` email
     * @return array
     * @throws BadRequestHttpException
     * @throws FirstValidationErrorHttpException
     */
    public function actionContacts()
    {
        $request = Yii::$app->getRequest();
        $store = Yii::$app->store->getInstance();

        $contactForm = new ContactForm();
        $contactForm->setStore($store);

        if (!$contactForm->load($request->post()) || !$contactForm->contact()) {
            if ($contactForm->hasErrors()) {
                throw new FirstValidationErrorHttpException($contactForm);
            } else {
                throw new BadRequestHttpException('Cannot send email!');
            }
        }

        return [
            'message' => $contactForm->getSentSuccess(),
        ];
    }

}
