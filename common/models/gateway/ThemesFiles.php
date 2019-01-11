<?php

namespace common\models\gateway;

use gateway\components\behaviors\ThemesFilesBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateway\queries\ThemesFilesQuery;

/**
 * This is the model class for table "{{%themes_files}}".
 *
 * @property int $id
 * @property int $theme_id
 * @property string $name
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 */
class ThemesFiles extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->gatewayDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%themes_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme_id', 'name', 'content'], 'required'],
            [['theme_id', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'theme_id' => Yii::t('app', 'Theme ID'),
            'name' => Yii::t('app', 'Name'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ThemesFilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ThemesFilesQuery(get_called_class());
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
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
            'assets' => ThemesFilesBehavior::class,
        ];
    }
}