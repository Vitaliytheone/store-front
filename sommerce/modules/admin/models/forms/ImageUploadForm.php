<?php
namespace sommerce\modules\admin\models\forms;

use common\components\cdn\BaseCdn;
use common\components\cdn\Cdn;
use common\models\store\Images;
use common\models\stores\Stores;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class BlockUploadForm
 * @package app\modules\superadmin\models\forms
 */
class ImageUploadForm extends Model
{
    const MAX_IMAGE_SIZE = 1024 * 1024 * 5;

    const PREVIEW_WIDTH = 40;
    const PREVIEW_HEIGHT = 40;

    /**
     * Uploaded file
     * @var UploadedFile
     */
    public $file;

    /**
     * @var Images
     */
    private $_image;

    /**
     * @var Stores
     */
    private $_store;

    /**
     * @var BaseCdn;
     */
    private $_cdn;

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
                ]
            ]
        ];
    }

    /**
     * Set CDN
     * @param BaseCdn $cdn
     */
    public function setCdn(BaseCdn $cdn)
    {
        $this->_cdn = $cdn;
    }

    /**
     * Get CDN
     * @return BaseCdn
     */
    public function getCdn()
    {
        return $this->_cdn;
    }

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store){
        $this->_store = $store;
    }

    /**
     * Get store
     * @return mixed
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * @return Images
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * @param Images $image
     */
    public function setImage(Images $image)
    {
        $this->_image = $image;
    }

    /**
     * Upload image
     * @return boolean
     * @throws
     */
    public function upload()
    {
        $this->file = UploadedFile::getInstanceByName('image');

        if (!$this->validate()) {
            return false;
        }

        if (!($this->file instanceof UploadedFile)) {
            $this->addError('file', Yii::t('admin', 'settings.message_upload_error'));
            return false;
        }

        if (!$content = file_get_contents($this->file->tempName)) {
            $this->addError('file', Yii::t('admin', 'settings.message_upload_error'));
            return false;
        }

        $this->setCdn(Cdn::getCdn());

        if (!$cdnId = $this->uploadFile($this->file->tempName, $this->file->type)) {
            return false;
        }

        if (!$url = $this->cdnGetUrl($cdnId)) {
            return false;
        }

        if (!$cdnInfo = $this->cdnGetInfo($cdnId)) {
            return false;
        }

        if (!$previewData = $this->cdnMakePreview($cdnId, self::PREVIEW_WIDTH, self::PREVIEW_HEIGHT)) {
            return false;
        }

        $image = new Images();
        $image->file_name = $this->file->name;
        $image->file = $content;
        $image->cdn_id = $cdnId;
        $image->cdn_data = json_encode($cdnInfo);
        $image->url = $url;
        $image->thumbnail_url = $previewData['url'];

        if (!$image->save(false)) {
            throw new Exception('Cannot save image!');
        }

        $this->setImage($image);

        return true;
    }

    /**
     * Delete image
     * @return bool
     * @throws Exception]
     */
    public function delete()
    {
        if (!$image = $this->getImage()) {
           throw new Exception('Image is not defined');
        }

        $this->setCdn(Cdn::getCdn());

        if (!$this->cdnDelete($image->cdn_id)) {
            return false;
        }

        if (!$image->delete()) {
            throw new Exception('Cannot delete image!');
        }

        return true;
    }

    /**
     * Delete file
     * @param $cdnId
     * @return bool
     */
    protected function cdnDelete($cdnId)
    {
        /** @var BaseCdn $cdn */
        $cdn = $this->getCdn();

        try {
            $cdn->delete($cdnId);
        } catch (\Exception $e) {
            $this->addError('file', Yii::t('admin', 'cdn.error.common'));
        }

        return true;
    }

    /**
     * Get uploaded file url
     * @param $cdnId
     * @return string
     */
    protected function cdnGetUrl($cdnId)
    {
        $data = null;

        /** @var BaseCdn $cdn */
        $cdn = $this->getCdn();

        try {
            $data = $cdn->getUrl($cdnId);
        } catch (\Exception $e) {
            $this->addError('file', Yii::t('admin', 'cdn.error.common'));
        }

        return $data;
    }

    /**
     * Get uploaded file info
     * @param $cdnId
     * @return array|null|object
     */
    protected function cdnGetInfo($cdnId)
    {
        $data = null;

        /** @var BaseCdn $cdn */
        $cdn = $this->getCdn();

        try {
            $data = $cdn->getInfo($cdnId);
        } catch (\Exception $e) {
            $this->addError('file', Yii::t('admin', 'cdn.error.common'));
        }

        return $data;
    }

    /**
     * Make uploaded file preview
     * @param $cdnId
     * @param $width
     * @param $height
     * @return array|null
     */
    protected function cdnMakePreview($cdnId, $width, $height)
    {
        $data = null;

        /** @var BaseCdn $cdn */
        $cdn = $this->getCdn();

        try {
            $data = $cdn->makePreview($cdnId, $width,  $height);
        } catch (\Exception $e) {
            $this->addError('file', Yii::t('admin', 'cdn.error.common'));
        }

        return $data;
    }

    /**
     * Upload file to CDN
     * @param $filePath
     * @param null $mime
     * @return null|string
     */
    protected function uploadFile($filePath, $mime = null)
    {
        $cdnId = null;

        /** @var BaseCdn $cdn */
        $cdn = $this->getCdn();

        try {
            $cdnId = $cdn->uploadFromPath($filePath, $mime);
        } catch (\Exception $e) {
            $this->addError('file', Yii::t('admin', 'cdn.error.common'));
        }

        return $cdnId;
    }

}