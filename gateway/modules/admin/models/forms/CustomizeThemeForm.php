<?php
namespace admin\models\forms;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class CustomizeThemeForm
 * @package admin\models\forms
 */
class CustomizeThemeForm
{
    private $_themeFolder;

    public function __construct($theme)
    {
        $this->_themeFolder = $theme;
    }

    /**
     * @param $postData
     * @return bool
     * @throws BadRequestHttpException
     */
    public function save($postData)
    {
        $editDataForm = EditThemeForm::make($this->_themeFolder, "data.json");
        if (!$editDataForm) {
            return false;
        }
        $editDataForm->setUser(Yii::$app->user);

        $content = stripslashes(json_encode(['data' => $postData], JSON_PRETTY_PRINT));
        if (!$editDataForm->updateThemeFile($content)) {
            throw new BadRequestHttpException();
        }

        $editDataForm->setFile('template.css');
        $content = $editDataForm->fetchFileContent();
        foreach ($postData as $key => $value) {
            $content = str_replace('{{ settings.' . $key . ' }}',  $value, $content);
        }
        $editDataForm->setFile('style.css');
        if (!$editDataForm->updateThemeFile($content)) {
            throw new BadRequestHttpException();
        }
        return true;
    }

    /**
     * @return string | null
     * @throws NotFoundHttpException
     */
    public function getConfigs()
    {
        $editDataForm = EditThemeForm::make($this->_themeFolder, "data.json");
        if (!$editDataForm) {
            return null;
        }
        $contentData = $editDataForm->fetchFileContent();
        $editDataForm->setFile('settings.json');
        $contentSettings = $editDataForm->fetchFileContent();
        if (!$contentData || !$contentSettings) {
            throw new NotFoundHttpException();
        }
        $result = array_merge(json_decode($contentSettings, true), json_decode($contentData, true));
        return json_encode($result);
    }

    /**
     * @return null|string
     */
    public function getTemplate()
    {
        $editStyleForm = EditThemeForm::make($this->_themeFolder, "template.css");
        if (!$editStyleForm) {
            return null;
        }
        return $editStyleForm->fetchFileContent();
    }
}