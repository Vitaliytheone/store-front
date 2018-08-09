<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use sommerce\modules\admin\models\forms\CustomizeThemeForm;
use Yii;
use yii\web\NotFoundHttpException;
use sommerce\modules\admin\components\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class ThemesCustomizerTrait
 * @property Controller $this
 * @package sommerce\modules\admin\controllers
 */
trait ThemesCustomizerTrait
{

    /**
     * Customize theme page
     * @param $theme
     * @return mixed
     */
    public function actionCustomizeTheme($theme)
    {
        $this->view->title = Yii::t('admin', "settings.customize_theme_pagetitle", [
            'theme' => null
        ]);
        $this->layout = '@admin/views/layouts/react_app';

        $urls = [
            'dataUrl' =>  Url::to(['/settings/theme-get-data', 'theme' => $theme], true),
            'stylesUrl' =>  Url::to(['/settings/theme-get-style', 'theme' => $theme], true),
            'iframeUrl' =>  Yii::$app->getHomeUrl(),
            'saveUrl' => Url::to(['/settings/theme-update-style', 'theme' => $theme], true)
        ];

        return $this->render('customize_theme', ['urls' => $urls]);
    }


    /**
     * @param $theme
     * @return null|string
     * @throws NotFoundHttpException
     */
    public function actionThemeGetStyle($theme)
    {
        $customizeForm = new CustomizeThemeForm($theme);
        $template = $customizeForm->getTemplate();
        if (!$template) {
            throw new NotFoundHttpException();
        }
        return $template;
    }

    /**
     * @param $theme
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionThemeUpdateStyle($theme)
    {
        $customizeForm = new CustomizeThemeForm($theme);
        $request = Yii::$app->getRequest();
        if (!$customizeForm->save($request->post())) {
            return [
                "success" => false,
                "error_messages" => Yii::t('admin', 'settings.themes_can_not_customize')
            ];
        }
        return [
            "success" => true,
            "error_meassages" => null
        ];
    }

    /**
     * @param $theme
     * @return null|string
     * @throws NotFoundHttpException
     */
    public function actionThemeGetData($theme)
    {
        $customizeForm = new CustomizeThemeForm($theme);
        $data = $customizeForm->getConfigs();
        if (!$data) {
            throw new NotFoundHttpException();
        }
        return $data;
    }
}