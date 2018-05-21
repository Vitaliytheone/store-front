<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\store\ActivityLog;
use common\models\stores\DefaultThemes;
use common\models\stores\StoreAdminAuth;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\ActivateThemeForm;
use sommerce\modules\admin\models\forms\CreateThemeForm;
use sommerce\modules\admin\models\forms\EditThemeForm;
use sommerce\modules\admin\models\search\ThemesSearch;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ThemesTrait
 * @property Controller $this
 * @package sommerce\modules\admin\controllers
 */
trait ThemesTrait {

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
        $themeModel->setUser(Yii::$app->user);

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

        $activatedThemeForm = new ActivateThemeForm();
        $activatedThemeForm->setUser(Yii::$app->user);


        UiHelper::message(Yii::t('admin', 'settings.themes_message_activated', [
            'theme_name' => $activatedThemeForm->activate($theme)->name,
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
        $this->addModule('adminThemes', ['extention' => pathinfo($file, PATHINFO_EXTENSION)]);

        $editThemeForm = EditThemeForm::make($theme, $file);

        if (!$editThemeForm) {
            return $this->redirect(Url::toRoute('/settings/themes'));
        }

        $editThemeForm->setUser(Yii::$app->user);

        if ($editThemeForm->load($request->post()) && $editThemeForm->updateThemeFile()) {
            UiHelper::message(Yii::t('admin', 'settings.themes_message_updated'));

            return $this->refresh();
        }

        return $this->render('edit_theme', [
            'currentFile' => $file,
            'theme' => $editThemeForm->getThemeModel(),
            'currentFileContent' => $editThemeForm->fetchFileContent(),
            'reset' => $editThemeForm->isResetAble(),
            'filesTree' => $editThemeForm->getFilesTree(),
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

        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity();

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_THEMES_THEME_FILE_RESETED, $themeModel->id, $themeModel->name);

        UiHelper::message(Yii::t('admin', 'settings.themes_message_reset'));

        return $this->redirect(Url::toRoute(['/settings/edit-theme', 'theme' => $theme, 'file' => $file]));
    }
}