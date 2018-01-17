<?php

namespace frontend\modules\admin\controllers;

use common\components\ActiveForm;
use common\models\store\Navigation;
use common\models\store\Pages;
use common\models\stores\DefaultThemes;
use common\models\stores\StoreFiles;
use frontend\helpers\UiHelper;
use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\ActivateThemeForm;
use frontend\modules\admin\models\forms\CreateProviderForm;
use frontend\modules\admin\models\forms\EditNavigationForm;
use frontend\modules\admin\models\forms\EditPageForm;
use frontend\modules\admin\models\forms\EditStoreSettingsForm;
use frontend\modules\admin\models\forms\EditThemeForm;
use frontend\modules\admin\models\forms\ProvidersListForm;
use frontend\modules\admin\models\forms\UpdatePositionsNavigationForm;
use frontend\modules\admin\models\search\LinksSearch;
use frontend\models\search\NavigationSearch;
use frontend\modules\admin\models\search\PagesSearch;
use frontend\modules\admin\models\search\ProvidersSearch;
use frontend\modules\admin\models\forms\EditPaymentMethodForm;
use frontend\modules\admin\models\search\PaymentMethodsSearch;
use frontend\modules\admin\models\search\ThemesSearch;
use frontend\modules\admin\models\search\UrlsSearch;
use frontend\modules\admin\models\forms\CreateThemeForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\BadRequestHttpException;


/**
 * Settings controller for the `admin` module
 */
class SettingsController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Add custom JS modules
//        $this->addModule('settings');
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
        if (StoreFiles::deleteStoreSettingsFile($type)) {
            UiHelper::message(Yii::t('admin', 'settings.message_image_deleted'));
        } else {
            UiHelper::message(Yii::t('admin', 'settings.message_image_delete_error'));
        }

        return $this->redirect(Url::toRoute('/settings'));
    }

    /**
     * Settings providers
     * @return string
     */
    public function actionProviders()
    {
        $this->view->title = 'Settings providers';
        
        $search = new ProvidersSearch();

        $this->addModule('adminProviders');

        $model = new ProvidersListForm();
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_provider_updated'));
            return $this->refresh();
        }

        return $this->render('providers', [
            'providers' => $search->search()
        ]);
    }

    /**
     * Settings payments. Payment methods list
     * @return string
     */
    public function actionPayments()
    {
        $this->view->title = Yii::t('admin', 'settings.payments_page_title');
        $this->addModule('adminPayments');

        $paymentMethods = PaymentMethodsSearch::findAll([
            'store_id' => yii::$app->store->getId(),
        ]);

        return $this->render('payments', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Settings payments. Payment method settings
     * @param $method
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionPaymentsSettings($method)
    {
        $request = yii::$app->getRequest();
        $storeId = yii::$app->store->getId();

        $this->view->title = Yii::t('admin', "settings.payments_edit_$method");

        $paymentModel = EditPaymentMethodForm::findOne([
            'store_id' => $storeId,
            'method' => $method,
        ]);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        if ($paymentModel->load($request->post()) && $paymentModel->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_settings_saved'));
            return $this->redirect(Url::toRoute(['/settings/payments']));
        }

        return $this->render('payments', [
            'method' => $method,
            'paymentModel' => $paymentModel,
        ]);
    }

    /**
     * Settings payments. Toggle payment method active AJAX action.
     * @param $method
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionPaymentsToggleActive($method)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $storeId = yii::$app->store->getId();

        if (!$request->isAjax) {
            exit;
        }

        $active = $request->post('active', null);
        if (is_null($active)) {
            throw new BadRequestHttpException();
        }

        $paymentModel = EditPaymentMethodForm::findOne([
            'store_id' => $storeId,
            'method' => $method,
        ]);

        if (!$paymentModel) {
            throw new NotFoundHttpException();
        }

        $paymentModel->setAttribute('active', $active|0);
        $paymentModel->save();

        return [
            'active' => $paymentModel->active,
        ];
    }

    /**
     * Settings themes
     * @return string
     */
    public function actionThemes()
    {
        $this->view->title = Yii::t('admin', "settings.themes_page_title");

        return $this->render('themes', [
            'themes' => (new ThemesSearch())->search(),
        ]);
    }

    /**
     * Create custom theme
     * @return string|Response
     */
    public function actionCreateTheme()
    {
        $this->view->title = Yii::t('admin', "settings.themes_create_title");
        $request = Yii::$app->getRequest();

        $themeModel = new CreateThemeForm();
        if($themeModel->create($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.themes_message_created'));

            return $this->redirect(Url::toRoute('/settings/themes'));
        }

        return $this->render('create_theme', ['theme' => $themeModel]);
    }

    /**
     * Activate theme
     * @param $theme
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionActivateTheme($theme)
    {
        $activatedTheme = ActivateThemeForm::activate($theme);

        UiHelper::message(Yii::t('admin', 'settings.themes_message_activated', [
            'theme_name' => $activatedTheme->name
        ]));

        return $this->redirect(Url::toRoute('/settings/themes'));
    }

    /**
     * Edit theme action
     * @param string $theme Current Theme folder name
     * @param string $file Relative file path to current Theme dir
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionEditTheme($theme, $file = null)
    {
        $request = Yii::$app->getRequest();
        $this->view->title = Yii::t('admin', 'settings.themes_edit_title');
        $this->addModule('adminThemes');

        $editThemeForm = EditThemeForm::make($theme, $file);

        if (!$editThemeForm) {
            throw new NotFoundHttpException('Theme or file not found!');
        }

        if ($editThemeForm->load($request->post()) && $editThemeForm->updateThemeFile()) {
            UiHelper::message(Yii::t('admin', 'settings.themes_message_updated'));

            return $this->refresh();
        }

        $fileContent = $editThemeForm->fetchFileContent();

        return $this->render('edit_theme', [
            'theme' => $editThemeForm->getThemeModel(),
            'currentFile' => $file,
            'currentFileContent' => $fileContent,
            'reset' => $editThemeForm->isResetAble(),
            'filesTree' => $editThemeForm::$filesTree,
        ]);
    }

    /**
     * Reset default theme file
     * @param $theme
     * @param $file
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionResetThemeFile($theme, $file)
    {
        $themeModel = DefaultThemes::findOne(['folder' => $theme]);

        if (!$themeModel) {
            throw new NotFoundHttpException();
        }

        if (!$themeModel->reset($file)) {
            $this->refresh();
        }

        UiHelper::message(Yii::t('admin', 'settings.themes_message_reset'));

        return $this->redirect(Url::toRoute(['/settings/edit-theme', 'theme' => $theme, 'file' => $file]));
    }

    /**
     * Settings pages
     * @return string
     */
    public function actionPages()
    {
        $this->view->title = Yii::t('admin', "settings.pages_page_title");
        $this->addModule('adminPages');

        $pages = (new PagesSearch())->searchPages();

        return $this->render('pages', [
            'pages' => $pages,
        ]);
    }

    /**
     * Create page
     * @return string|Response
     */
    public function actionCreatePage()
    {
        $this->view->title = Yii::t('admin', "settings.pages_create_page");

        $request = Yii::$app->getRequest();

        $pageModel = new EditPageForm();

        if ($pageModel->create($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.pages_message_created'));
            return $this->redirect(Url::toRoute('/settings/pages'));
        }

        $urlsModel = new UrlsSearch();
        $exitingUrls = $urlsModel->searchUrls();

        $this->addModule('adminPageEdit', [
            'urls' => $exitingUrls,
            'url_error' => $pageModel->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'page' => $pageModel,
            'storeUrl' => Yii::$app->store->getInstance()->domain,
        ]);
    }

    /**
     * Edit page
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionEditPage($id)
    {
        $this->view->title = Yii::t('admin', "settings.pages_edit_page");

        $request = Yii::$app->getRequest();

        $pageModel = EditPageForm::findOne($id);
        if (!$pageModel) {
            throw new NotFoundHttpException();
        }

        if ($pageModel->edit($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.pages_message_updated'));
            return $this->redirect(Url::toRoute('/settings/pages'));
        }

        $this->addModule('adminPageEdit', [
            'url_error' => $pageModel->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'page' => $pageModel,
            'storeUrl' => Yii::$app->store->getInstance()->domain,
        ]);
    }

    /**
     * Virtual deleting `page`
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeletePage($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $pageModel = Pages::findOne($id);
        if (!$pageModel) {
            throw new NotFoundHttpException();
        }

        $pageModel->deleteVirtual();

        UiHelper::message(Yii::t('admin', 'settings.pages_message_deleted'));

        return [true];
    }

    /**
     * Settings blocks
     * @return string
     */
    public function actionBlocks()
    {
        return $this->render('blocks');
    }

    /**
     * Settings navigation
     * @return string
     */
    public function actionNavigation()
    {
        $this->view->title = Yii::t('admin', 'settings.nav_page_title');

        $model = new EditNavigationForm();
        $search = new NavigationSearch();

        return $this->render('navigation', [
            'linkTypes' => $model::linkTypes(),
            'navTree' => $search->getTree(),
        ]);
    }

    /**
     * Create new Navigation item
     * @return array
     */
    public function actionCreateNav()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = new EditNavigationForm();

        if (!$model->load($request->post()) || !$model->save()) {
            return ['error' => ActiveForm::firstError($model)];
        }

        UiHelper::message(Yii::t('admin', 'settings.nav_message_created'));

        return [
            'product' => $model->getAttributes(),
        ];
    }

    /**
     * Update exiting Navigation item
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUpdateNav($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = EditNavigationForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->load($request->post()) || !$model->save()) {
            return ['error' => ActiveForm::firstError($model)];
        }

        UiHelper::message(Yii::t('admin', 'settings.nav_message_updated'));

        return ['model' => $model->getAttributes()];
    }

    /**
     * Return Navigation item AJAX action
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetNav($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = Navigation::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return ['model' => $model->getAttributes()];
    }

    /**
     * Delete Navigation item AJAX action
     * Mark package as deleted
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteNav($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = Navigation::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->deleteVirtual();

        UiHelper::message(Yii::t('admin', 'settings.nav_message_deleted'));

        return [true];
    }

    /**
     * Update Navigation items positions after drag&drop AJAX action
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionUpdatePositionsNav()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = new UpdatePositionsNavigationForm();
        if (!$model->updatePositions($request->post())) {
            throw new BadRequestHttpException();
        }

        return [true];
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

    /**
     * Create provider
     *
     * @access public
     * @return mixed
     */
    public function actionCreateProvider()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CreateProviderForm();
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_provider_created'));
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }
}
