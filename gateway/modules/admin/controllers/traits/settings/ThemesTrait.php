<?php
namespace admin\controllers\traits\settings;

use common\models\gateway\ThemesFiles;
use common\models\gateways\Admins;
use common\models\gateways\DefaultThemes;
use gateway\controllers\CommonController;
use gateway\helpers\UiHelper;
use admin\components\Url;
use admin\models\forms\ActivateThemeForm;
use admin\models\forms\EditThemeForm;
use admin\models\search\ThemesSearch;
use Yii;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ThemesTrait
 * @property CommonController $this
 * @package admin\controllers
 */
trait ThemesTrait {

    /**
     * Settings themes
     * @return string
     */
    public function actionThemes()
    {
        $this->view->title = Yii::t('admin', "settings.themes_page_title");
        $search = new ThemesSearch();
        $search->setGateway($this->gateway);

        return $this->render('themes', [
            'themes' => $search->search(),
        ]);
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
        $activatedThemeForm->setGateway($this->gateway);

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
        $this->view->title = Yii::t('admin', 'settings.themes_edit_title');
        $this->addModule('adminThemes', [
            'filename' => $file,
            'extension' => pathinfo($file, PATHINFO_EXTENSION)
        ]);

        $editThemeForm = EditThemeForm::make($theme, $file);

        if (!$editThemeForm) {
            return $this->redirect(Url::toRoute('/settings/themes'));
        }

        $editThemeForm->setUser(Yii::$app->user);

        return $this->render('edit_theme', [
            'currentFile' => $file,
            'theme' => $editThemeForm->getThemeModel(),
            'currentFileContent' => $editThemeForm->fetchFileContent(),
            'reset' => $editThemeForm->isResetAble(),
            'filesTree' => $editThemeForm->getFilesTree(),
        ]);
    }

    /**
     * Update theme file AJAX action
     * @param $theme
     * @param $file
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateTheme($theme, $file)
    {
        $request = Yii::$app->getRequest();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            throw new BadRequestHttpException();
        }

        $editThemeForm = EditThemeForm::make($theme, $file);
        $editThemeForm->setUser(Yii::$app->user);


        if (!$editThemeForm) {
            throw new NotFoundHttpException();
        }

        if (!$modifiedAt = $editThemeForm->updateThemeFile($request->post())) {
            throw new BadRequestHttpException();
        }

        return [
            'success' => true,
            'filename' => $file,
            'modified_at' => Html::tag('span',
                Yii::t('admin', 'settings.themes_modified') . ' ' . $modifiedAt,
                ['class' => 'jstree-tooltip']
            ),
            'resetable' =>  $editThemeForm->isResetAble(),
            'message' => Yii::t('admin', Yii::t('admin', 'settings.themes_message_updated'))
        ];
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

        if (!ThemesFiles::deleteAll([
            'theme_id' => $themeModel->id,
            'name' => $file
        ])) {
            $this->refresh();
        }

        UiHelper::message(Yii::t('admin', 'settings.themes_message_reset'));

        return $this->redirect(Url::toRoute(['/settings/edit-theme', 'theme' => $theme, 'file' => $file]));
    }
}