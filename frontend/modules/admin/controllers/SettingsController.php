<?php

namespace frontend\modules\admin\controllers;

use common\models\store\Files;
use frontend\helpers\UiHelper;
use frontend\modules\admin\components\Url;
use frontend\modules\admin\controllers\traits\settings\BlocksTrait;
use frontend\modules\admin\controllers\traits\settings\NavigationTrait;
use frontend\modules\admin\controllers\traits\settings\PagesTrait;
use frontend\modules\admin\controllers\traits\settings\PaymentsTrait;
use frontend\modules\admin\controllers\traits\settings\ProvidersTrait;
use frontend\modules\admin\controllers\traits\settings\ThemesTrait;
use frontend\modules\admin\models\forms\EditStoreSettingsForm;
use frontend\modules\admin\models\search\LinksSearch;
use Yii;
use yii\web\Response;

/**
 * Settings controller for the `admin` module
 */
class SettingsController extends CustomController
{
    use BlocksTrait;
    use ProvidersTrait;
    use NavigationTrait;
    use ThemesTrait;
    use PaymentsTrait;
    use PagesTrait;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Add custom JS modules
        // $this->addModule('settings');
        return parent::beforeAction($action);
    }

    /**
     * Settings general
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();

        $this->view->title = Yii::t('admin', 'settings.page_title');
        $this->addModule('adminGeneral');

        /** @var \common\models\stores\Stores $store */
        $store = Yii::$app->store->getInstance();
        $storeForm = EditStoreSettingsForm::findOne($store->id);

        if ($storeForm->updateSettings($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_updated'));
            return $this->refresh();
        }

        return $this->render('index', [
            'store' => $storeForm,
            'timezones' => Yii::$app->params['timezone'],
        ]);
    }

    /**
     * Delete Store Favicon or Logo
     * @param $type
     * @return Response
     */
    public function actionDeleteImage($type)
    {
        if (Files::deleteStoreSettingsFile($type)) {
            UiHelper::message(Yii::t('admin', 'settings.message_image_deleted'));
        } else {
            UiHelper::message(Yii::t('admin', 'settings.message_image_delete_error'));
        }

        return $this->redirect(Url::toRoute('/settings'));
    }

    /**
     * Return links list by link type AJAX action
     * @param $link_type
     * @return array
     */
    public function actionGetLinks($link_type)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $searchModel = new LinksSearch();

        return ['links' => $searchModel->searchLinksByType($link_type|0)];
    }
}
