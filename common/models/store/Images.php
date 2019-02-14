<?php

namespace common\models\store;

use Yii;
use yii\db\ActiveRecord;
use common\models\store\queries\ImagesQuery;
use yii\db\Connection;

/**
 * This is the model class for table "{{%images}}".
 *
 * @property int $id
 * @property string $file_name
 * @property resource $file File content
 * @property string $cdn_id
 * @property string $cdn_data
 * @property string $url
 * @property string $thumbnail_url
 * @property int $created_at
 */
class Images extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%images}}';
    }

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'string'],
            [['created_at'], 'integer'],
            [['file_name', 'cdn_id'], 'string', 'max' => 100],
            [['cdn_data'], 'string', 'max' => 1000],
            [['url', 'thumbnail_url'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file_name' => Yii::t('app', 'File Name'),
            'file' => Yii::t('app', 'File content'),
            'cdn_id' => Yii::t('app', 'Cdn ID'),
            'cdn_data' => Yii::t('app', 'Cdn Data'),
            'url' => Yii::t('app', 'Url'),
            'thumbnail_url' => Yii::t('app', 'Thumbnail Url'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ImagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ImagesQuery(get_called_class());
    }
}
