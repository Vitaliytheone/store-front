<?php
namespace sommerce\modules\admin\models\forms;

use common\components\cdn\BaseCdn;
use common\models\store\Blocks;
use common\models\store\Files;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class BlockUploadForm
 * @package app\modules\superadmin\models\forms
 */
class BlockUploadForm extends Model {

    const MAX_IMAGE_SIZE = 1024 * 1024 * 5;

    public $file;

    public $link;

    /**
     * @var Blocks
     */
    private $_block;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'png, jpg, gif', 'maxSize' => static::MAX_IMAGE_SIZE, 'mimeTypes' => [
                    BaseCdn::MIME_JPEG,
                    BaseCdn::MIME_PNG,
                    BaseCdn::MIME_GIF,
                    BaseCdn::MIME_SVG
                ]
            ]
        ];
    }

    /**
     * Set block
     * @param Blocks $block
     */
    public function setBlock($block)
    {
        $this->_block = $block;
    }

    /**
     * Save block content
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $fileInstance = UploadedFile::getInstanceByName('file');

        if (!($fileInstance instanceof UploadedFile)) {
            $this->addError('file', Yii::t('admin', 'settings.message_cdn_upload_error'));
            return false;
        }

        $tmpFilePath = $fileInstance->tempName;
        $mime = $fileInstance->type;

        $fileModel = new Files();

        $cdnId = $fileModel->uploadFile($tmpFilePath, static::getTypeByCode($this->_block->code), $mime);
        $this->link = $fileModel->getUrl();

        if (!$cdnId || !$this->link) {
            $this->addError('file', Yii::t('admin', 'settings.message_cdn_upload_error'));
            return false;
        }

        return true;
    }

    /**
     * Get file type by code
     * @param $code
     * @return mixed
     */
    public static function getTypeByCode($code)
    {
        return ArrayHelper::getValue([
            Blocks::CODE_SLIDER => Files::FILE_TYPE_SLIDER,
            Blocks::CODE_FEATURES => Files::FILE_TYPE_FEATURES,
            Blocks::CODE_REVIEW => Files::FILE_TYPE_REVIEW,
            Blocks::CODE_PROCESS => Files::FILE_TYPE_STEPS,
        ], $code);
    }
}