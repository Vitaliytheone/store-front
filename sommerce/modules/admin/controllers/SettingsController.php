<?php

namespace sommerce\modules\admin\controllers;

use common\components\response\CustomResponse;
use common\models\sommerce\Files;
use sommerce\helpers\ConfigHelper;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\controllers\traits\settings\IntegrationsTrait;
use sommerce\modules\admin\controllers\traits\settings\LanguageTrait;
use sommerce\modules\admin\controllers\traits\settings\NotificationsTrait;
use sommerce\modules\admin\controllers\traits\settings\PaymentsTrait;
use sommerce\modules\admin\controllers\traits\settings\ProvidersTrait;
use sommerce\modules\admin\models\forms\EditStoreSettingsForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Settings controller for the `admin` module
 */
class SettingsController extends CustomController
{
    use ProvidersTrait;
    use PaymentsTrait;
    use LanguageTrait;
    use NotificationsTrait;
    use IntegrationsTrait;

    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        return $parentBehaviors + [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'add-payment-method',
                    'integrations-toggle-active',
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add-payment-method' => ['POST'],
                    'edit-integration' => ['GET', 'POST'],
                    'integrations' => ['GET'],
                    'integrations-toggle-active' => ['POST'],
                ],
            ],
            'jqueryApi' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'add-payment-method',
                    'integrations-toggle-active',
                ],
                'formats' => [
                    'application/json' => CustomResponse::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Settings general
     * @return string|Response
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();

        $this->view->title = Yii::t('admin', 'settings.page_title');
        $this->addModule('adminGeneral');

        $storeForm = EditStoreSettingsForm::findOne($this->store->id);

        /** @var \common\models\sommerces\StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);
        $storeForm->setUser($identity);

        if ($storeForm->updateSettings($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_updated'));
            return $this->refresh();
        }

        $filesLimits = $storeForm->getUploadedFilesLimits();
        return $this->render('index', [
            'store' => $storeForm,
            'timezones' => Yii::$app->params['timezone'],
            'currencies' => ConfigHelper::getCurrenciesList(),
            'iconFileSizeLimit' => $filesLimits['iconFileSizeLimit'],
            'logoFileSizeLimit' => $filesLimits['logoFileSizeLimit'],
        ]);
    }

    /**
     * Delete Store Favicon or Logo
     * @param $type
     * @return Response
     * @throws \yii\base\Exception
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
     * Check if currency changes and return bool
     * @var $postData string
     * @return bool|null
     */
    public function actionCheckCurrency(): ?bool
    {
        if (!Yii::$app->getRequest()->isAjax) {
            exit;
        }

        $postData = Yii::$app->getRequest()->post('currency');

        if (empty($postData)) {
            return null;
        }

        $storeForm = EditStoreSettingsForm::findOne($this->store->id);

        if (empty($storeForm)) {
            return null;
        }

        return $storeForm->currencyChange($postData);
    }
}
