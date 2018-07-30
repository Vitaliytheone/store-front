<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use sommerce\modules\admin\components\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use sommerce\modules\admin\models\forms\EditThemeForm;

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
        $request = Yii::$app->getRequest();
        if (!$request->isAjax) {
            throw new BadRequestHttpException();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $editStyleForm = EditThemeForm::make($theme, "template.css");
        return $editStyleForm->fetchFileContent();
    }

    public function actionThemeUpdateStyle($theme)
    {
        $request = Yii::$app->getRequest();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            throw new BadRequestHttpException();
        }

        $editDataForm = EditThemeForm::make($theme, "data.json");
        $editDataForm->setUser(Yii::$app->user);
        $data = $request->post();
        $params = [
            'file_content' =>  stripslashes(
                json_encode(['data' => $data], JSON_PRETTY_PRINT)
            )
        ];
        if (!$modifiedAt = $editDataForm->updateThemeFile($params, false)) {
            throw new BadRequestHttpException();
        }

        $editDataForm->setFile('template.css');
        $content = $editDataForm->fetchFileContent();
        foreach ($data as $key => $value) {
            $content = str_replace('{{ settings.' . $key . ' }}',  $value, $content);
        }
        $editDataForm->setFile('style.css');
        if (!$modifiedAt = $editDataForm->updateThemeFile(['file_content' => $content], false)) {
            throw new BadRequestHttpException();
        }
        return [
            "success" => true,
            "error_message" => null
        ];
    }

    public function actionThemeGetData($theme)
    {
        $request = Yii::$app->getRequest();
        if (!$request->isAjax) {
            throw new BadRequestHttpException();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $editDataForm = EditThemeForm::make($theme, "data.json");
        $contentData = $editDataForm->fetchFileContent();
        $editDataForm->setFile('settings.json');
        $contentSettings = $editDataForm->fetchFileContent();
        $result = array_merge(JSON::decode($contentSettings, true), JSON::decode($contentData, true));
        if (!$contentData || !$contentSettings) {
            throw new NotFoundHttpException();
        }

        return JSON::encode($result);
    }
}