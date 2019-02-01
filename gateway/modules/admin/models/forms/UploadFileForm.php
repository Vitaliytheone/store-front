<?php
namespace admin\models\forms;

use common\models\gateway\Files;
use common\models\gateways\Sites;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class UploadFileForm
 * @package admin\models\forms
 */
class UploadFileForm extends Model
{
    public $id;
    public $file;

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => Files::$availableExtensions[Files::FILE_TYPE_IMAGE], 'maxSize' => Files::IMAGE_SIZE],
        ];
    }

    /**
     * @param Sites $gateway
     */
    public function setGateway(Sites $gateway)
    {
        $this->_gateway = $gateway;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->file = UploadedFile::getInstance($this, 'file');

        if (!$this->validate()) {
            return false;
        }

        $model = new Files();
        $model->file_type = Files::FILE_TYPE_IMAGE;
        $model->mime = FileHelper::getMimeType($this->file->tempName);
        $model->name = $this->file->name;
        $model->content = is_file($this->file->tempName) ? file_get_contents($this->file->tempName) : null;

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        $this->id = $model->id;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => Yii::t('admin', 'settings.files.create_file.field.file'),
        ];
    }
}