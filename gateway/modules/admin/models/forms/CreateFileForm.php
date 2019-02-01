<?php
namespace admin\models\forms;

use common\models\gateway\Files;
use common\models\gateways\Sites;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class CreateFileForm
 * @package admin\models\forms
 */
class CreateFileForm extends Model
{
    public $id;
    public $name;
    public $file;
    public $file_type;

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
            [['file_type'], 'required'],

            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'twig', 'checkExtensionByMimeType' => false, 'when' => function() {
                return in_array($this->file_type, [
                    Files::FILE_TYPE_PAGE,
                    Files::FILE_TYPE_SNIPPET,
                ]);
            }, 'maxSize' => Files::TWIG_SIZE],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'css', 'checkExtensionByMimeType' => false, 'when' => function() {
                return in_array($this->file_type, [
                    Files::FILE_TYPE_CSS,
                ]);
            }, 'maxSize' => Files::CSS_SIZE],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'js', 'checkExtensionByMimeType' => false, 'when' => function() {
                return in_array($this->file_type, [
                    Files::FILE_TYPE_JS,
                ]);
            }, 'maxSize' => Files::JS_SIZE],

            [['name'], 'required', 'when' => function() {
                return empty($this->file);
            }],

            [['name'], 'string'],
            [['name'], 'match', 'pattern' => '/^([\w\d\-\_\s])*$/uis'],
            [['file_type'], 'in', 'range' => [
                Files::FILE_TYPE_JS,
                Files::FILE_TYPE_CSS,
                Files::FILE_TYPE_PAGE,
                Files::FILE_TYPE_SNIPPET,
            ]],
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
        $model->file_type = $this->file_type;

        if (!empty($this->file)) {
            $model->name = $this->file->name;
            $model->mime = FileHelper::getMimeType($this->file->tempName);
            $model->content = is_file($this->file->tempName) ? file_get_contents($this->file->tempName) : null;
        } else {
            $model->name = $this->name . '.' . Files::$availableExtensions[$this->file_type];
        }

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
            'name' => Yii::t('admin', 'settings.files.create_file.field.name'),
            'file' => Yii::t('admin', 'settings.files.create_file.field.file'),
        ];
    }
}