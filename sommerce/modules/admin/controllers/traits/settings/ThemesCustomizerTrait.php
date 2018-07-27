<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use sommerce\modules\admin\components\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;

trait ThemesCustomizerTrait
{
    /**
     * Customize theme page
     * @return string
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


    public function actionThemeGetStyle($theme)
    {
        $resource = 'custom.css';
        $dir= Yii::getAlias('@defaultThemes/' . $theme);
        if (!is_dir($dir)) {
            $file = Yii::getAlias('@customThemes/' . $theme);
            if (!is_dir($file)) {
               throw new NotFoundHttpException();
            }
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $content = file_get_contents($dir .  '/' . $resource);
        if (!$content) {
            throw new NotFoundHttpException();
        }
        return $content;
    }

    public function actionThemeUpdateStyle($theme)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isAjax || !$request->isPost) {
            throw new BadRequestHttpException('Ajax request expected!');
        }
        $data = $request->post();
        $resource = 'custom.css.template';
        $dir= Yii::getAlias('@defaultThemes/' . $theme);
        if (!is_dir($dir)) {
            $file = Yii::getAlias('@customThemes/' . $theme);
            if (!is_dir($file)) {
                throw new NotFoundHttpException();
            }
        }
        $content = file_get_contents($dir .  '/' . $resource);
        foreach ($data as $key => $value) {
            $content = str_replace('{{ settings.' . $key . ' }}',  $value, $content);
        }
        $path = $dir .  '/style.css';

        if (!file_put_contents($path, $content)) {
           return false;
        }
        $path = $dir .  '/data.json';
        $dataContent = JSON::encode(['data' => $data]);
        if (!file_put_contents($path, $dataContent)) {
            return false;
        }
        return [
            "success" => true,
            "error_message" => null
        ];
    }

    public function actionThemeGetData($theme)
    {
        $data = 'data.json';
        $settings = 'settings.json';

        $dir= Yii::getAlias('@defaultThemes/' . $theme);
        if (!is_dir($dir)) {
            $file = Yii::getAlias('@customThemes/' . $theme);
            if (!is_dir($file)) {
                throw new NotFoundHttpException();
            }
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        $contentData = file_get_contents($dir .  '/' . $data);
        $contentSettings = file_get_contents($dir .  '/' . $settings);

        $result = array_merge(JSON::decode($contentSettings, true), JSON::decode($contentData, true));

        if (!$contentData || !$contentSettings) {
            throw new NotFoundHttpException();
        }
        return JSON::encode($result);
    }
}