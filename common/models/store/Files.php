<?php

namespace common\models\store;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\components\cdn\Cdn;
use common\components\cdn\BaseCdn;
use common\models\store\queries\FilesQuery;
use common\models\stores\Stores;


/**
 * This is the model class for table "{{%files}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $date
 * @property integer $created_at
 *
 * @property Stores $store
 */
class Files extends ActiveRecord
{
    const FILE_TYPE_LOGO = 1;
    const FILE_TYPE_SLIDER = 2;
    const FILE_TYPE_FAVICON = 3;
    const FILE_TYPE_FEATURES = 4;
    const FILE_TYPE_REVIEW = 5;
    const FILE_TYPE_STEPS = 6;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_at'], 'integer'],
            [['date'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'date' => Yii::t('app', 'Date'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return FilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FilesQuery(get_called_class());
    }

    /**
     * Upload file to CDN
     * @param $filePath
     * @param $storeFileType
     * @param null $mime
     * @return null|string
     */
    public function uploadFile($filePath, $storeFileType, $mime = null)
    {
        /** @var BaseCdn $cdn */
        $cdn = Cdn::getCdn();
        $cdnId = null;

        try {
            $cdnId = $cdn->uploadFromPath($filePath, $mime);
        } catch (\Exception $e) {
            return null;
        }

        $this->setAttributes([
            'date' => $cdnId,
            'type' => $storeFileType,
        ]);

        if (!$this->save()) {
            return null;
        }

        return $cdnId;
    }

    /**
     * Upload Favicon and Logo files
     * @param $fileType
     * @param $filePath
     * @param null $mime
     * @return null|static
     * @throws Exception
     */
    public static function updateStoreSettingsFile($fileType, $filePath, $mime = null)
    {
        $urlFields = [
            self::FILE_TYPE_LOGO => 'logo',
            self::FILE_TYPE_FAVICON => 'favicon',
        ];

        if (!array_key_exists($fileType, $urlFields)) {
            throw new Exception('Unknown file type: ' . $fileType);
        }

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        $file = static::findOne([
            'type' => $fileType,
        ]);

        if (!$file) {
            $file = new static();
        }

        $oldFileCdnId = $file->date;

        $cdnId = $file->uploadFile($filePath, $fileType, $mime);
        $url = $file->getUrl();

        if (!$cdnId || !$url) {
            return null;
        }

        $store->setAttributes([
            $urlFields[$fileType] => $url
        ]);

        if (!$store->save(false)) {
            return null;
        }

        if ($oldFileCdnId) {
            static::deleteFromCdn($oldFileCdnId);
        }

        return $file;
    }

    /**
     * Delete store Favicon or Logo
     * @param $fileType
     * @return bool
     * @throws Exception
     */
    public static function deleteStoreSettingsFile($fileType)
    {
        $urlFields = [
            self::FILE_TYPE_LOGO => 'logo',
            self::FILE_TYPE_FAVICON => 'favicon',
        ];

        if (!array_key_exists($fileType, $urlFields)) {
            throw new Exception('Unknown file type: ' . $fileType);
        }

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        $file = static::findOne([
            'type' => $fileType,
        ]);

        if (!$file || !$file->delete()) {
            return false;
        }

        $store->setAttributes([
            $urlFields[$fileType] => null
        ]);

        return $store->save();
    }

    /**
     * Return file cdn url
     * @return bool|string
     */
    public function getUrl()
    {
        /** @var BaseCdn $cdn */
        $cdn = Cdn::getCdn();

        try {
            $cdnUrl = $cdn->getUrl($this->date);
        } catch (\Exception $e) {
            return false;
        }

        return $cdnUrl;
    }

    /**
     * Delete file from CDN
     * @param string $cdnId
     * @return bool
     */
    public static function deleteFromCdn($cdnId)
    {
        /** @var BaseCdn $cdn */
        $cdn = Cdn::getCdn();

        try {
            $cdn->delete($cdnId);
        } catch (\Exception $e) {
            error_log('Error while deleting file from CDN! ' . $e);
            return false;
        }

        return true;
    }

    /**
     * Delete file from DB and CDN
     * @return false|int
     */
    public function delete($fromCdn = true)
    {
        if (!$this->isNewRecord && $fromCdn) {
            static::deleteFromCdn($this->date);
        }

        return parent::delete();
    }
}
