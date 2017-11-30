<?php

namespace frontend\modules\admin\models\forms;

use common\models\stores\Stores;
use yii\web\UploadedFile;
use Uploadcare;



/**
 * Class EditStoreSettingsForm
 * @property UploadedFile $faviconFile
 * @property UploadedFile $logoFile
 * @package frontend\modules\admin\models\forms
 */
class EditStoreSettingsForm extends Stores
{
    public $faviconFile;
    public $logoFile;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return "SettingsGeneralForm";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timezone',], 'integer'],
            [['name', 'seo_title',], 'string', 'max' => 255],
            [['seo_keywords', 'seo_description'], 'string', 'max' => 2000],

            ['logoFile',    'file', 'extensions' => 'png, jpg, gif', 'maxSize' => 3000000, 'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif']],
            ['faviconFile', 'file', 'extensions' => 'png, jpg, gif', 'maxSize' => 500000, 'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif']],
        ];
    }

    /**
     * Update General settings
     * @param $postData
     * @return bool
     */
    public function updateSettings($postData)
    {
        if (!$this->load($postData)) {
            return false;
        }

        $this->faviconFile = UploadedFile::getInstance($this, 'faviconFile');
        $this->logoFile = UploadedFile::getInstance($this, 'logoFile');

        if (!$this->validate()) {
            return false;
        }

        if ($this->logoFile) {

            $tempFilePath = $this->logoFile->tempName;
            $name = $this->logoFile->name;
            $extention =  $this->logoFile->extension;
            $mime = $this->logoFile->type;

            $api = new Uploadcare\Api('2b57ca4e85ca588704a4', '15b9d150192983929861');
            $file = $api->uploader->fromPath($tempFilePath, $mime);
            $file->store();
            $url = $file->getUrl();

            error_log(print_r($this->logoFile, 1), 0);
            error_log(print_r($url, 1), 0);

            $this->logo = $url;
        }


        $this->save(false);

        return true;
    }

    private function _uploadToCdn($pathToFile)
    {

    }


}