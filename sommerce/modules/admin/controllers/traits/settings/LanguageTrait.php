<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\stores\Stores;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\models\forms\ActivateLanguageForm;
use sommerce\modules\admin\models\forms\EditLanguageForm;
use sommerce\modules\admin\models\search\LanguagesSearch;
use Yii;
use sommerce\modules\admin\components\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Trait LanguageTrait
 * @package sommerce\modules\admin\controllers\traits\settings
 */
trait LanguageTrait {

    /**
     * Return available languages for current store
     * @return mixed
     */
    public function actionLanguages()
    {
        $this->view->title = Yii::t('admin', 'settings.languages_page_title');

        $this->addModule('adminStoreLanguages', [
            'action_activate_lang_url' => Url::toRoute(['/settings/activate-language', 'code' => '']),
            'action_add_lang_url' => Url::toRoute(['/settings/add-language', 'code' => '']),
            'success_redirect_url' => Url::toRoute(['/settings/languages']),
        ]);

        $langSearch = new LanguagesSearch();
        $langSearch->setStore(Yii::$app->store->getInstance());

        return $this->render('languages', [
            'storeLanguages' => $langSearch->storeLanguages(),
            'availableLanguages' => $langSearch->availableLanguages(),
        ]);
    }

    /**
     * Activate store language AJAX action
     * @param $code string language code
     * @return array
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionActivateLanguage($code)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        if (!$store || !$store instanceof Stores) {
            throw new BadRequestHttpException();
        }

        $form = new ActivateLanguageForm();
        $form->setStore($store);

        if (!$form->activateStoreLanguage($code)) {
            throw new BadRequestHttpException();
        }

        return ['code' => $store->language];
    }

    /**
     * Add store language AJAX action
     * @param $code
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAddLanguage($code)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        if (!$store || !$store instanceof Stores) {
            throw new BadRequestHttpException();
        }

        $form = new ActivateLanguageForm();
        $form->setStore($store);

        if (!$form->addStoreLanguage($code)) {
            throw new BadRequestHttpException();
        }

        UiHelper::message(Yii::t('admin', 'settings.languages_message_created'));

        return ['result' => true];
    }

    /**
     * Edit language action
     * @param $code
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditLanguage($code)
    {
        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        if (!$store || !$store instanceof Stores) {
            throw new NotFoundHttpException();
        }

        $request = Yii::$app->getRequest();

        $editForm = new EditLanguageForm();
        $editForm->code = $code;
        $editForm->setStore($store);

        if ($editForm->save($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.languages_message_updated'));
            return $this->refresh();
        }

        if (!$editForm->fetchMessages()) {
            throw new NotFoundHttpException();
        }

        return $this->render('edit_language', [
            'form' => $editForm,
        ]);
    }
}