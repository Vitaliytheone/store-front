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
            [['file'], 'file', 'extensions' => Files::$availableExtensions[Files::FILE_TYPE_IMAGE], 'maxSize' => Files::IMAGE_SIZE, 'maxFiles' => 5],
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
        $this->file = UploadedFile::getInstances($this, 'file');

        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        foreach ($this->file as $file) {
            $model = new Files();
            $model->file_type = Files::FILE_TYPE_IMAGE;
            $model->name =$file->name;
            $model->mime = FileHelper::getMimeType($file->tempName);
            $model->content = is_file($file->tempName) ? file_get_contents($file->tempName) : null;

            if (!$model->save()) {
                $transaction->rollBack();
                $this->addErrors($model->getErrors());
                return false;
            }
        }

        $transaction->commit();

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