<?php

namespace sommerce\modules\admin\controllers;

use common\models\store\Files;
use sommerce\helpers\ConfigHelper;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\controllers\traits\settings\BlocksTrait;
use sommerce\modules\admin\controllers\traits\settings\NavigationTrait;
use sommerce\modules\admin\controllers\traits\settings\NotificationsTrait;
use sommerce\modules\admin\controllers\traits\settings\PagesTrait;
use sommerce\modules\admin\controllers\traits\settings\PaymentsTrait;
use sommerce\modules\admin\controllers\traits\settings\ProvidersTrait;
use sommerce\modules\admin\controllers\traits\settings\ThemesTrait;
use sommerce\modules\admin\controllers\traits\settings\ThemesCustomizerTrait;
use sommerce\modules\admin\controllers\traits\settings\LanguageTrait;
use sommerce\modules\admin\models\forms\EditStoreSettingsForm;
use sommerce\modules\admin\models\search\LinksSearch;
use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;

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
    use LanguageTrait;
    use NotificationsTrait;
    use ThemesCustomizerTrait;

    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        return $parentBehaviors + [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'theme-get-style',
                    'theme-get-data',
                    'theme-update-style',
                    'add-payment-method',
                    'update-payment-positions',
                    'delete-invalid-currency',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'customize-theme' => ['GET'],
                    'theme-get-style' => ['GET'],
                    'theme-get-data' => ['GET'],
                    'theme-update-style' => ['POST'],
                    'add-payment-method' => ['POST'],
                    'update-payment-positions' => ['POST'],
                    'delete-invalid-currency' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['theme-update-style', 'add-payment-method','update-payment-positions','delete-invalid-currency'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Disabled csrf validation for some ajax actions
        if (in_array($action->id, [
            'update-blocks',
            'block-upload',
            'update-theme',
            'theme-update-style'
        ])) {
            $this->enableCsrfValidation = false;
        }
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

        $storeForm = EditStoreSettingsForm::findOne($this->store->id);

        $storeForm->setUser(Yii::$app->user);

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
        $searchModel->setStore($this->store);

        return ['links' => $searchModel->searchLinksByType($link_type|0)];
    }
}
