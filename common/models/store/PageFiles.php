<?php

namespace common\models\store;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\store\queries\FilesQuery;
use common\models\stores\Stores;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property integer $id
 * @property integer $name
 * @property string $content
 * @property string $json
 * @property string $json_draft
 * @property integer $file_type
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Stores $store
 */
class PageFiles extends ActiveRecord
{
    const NAME_JS = 'js';
    const NAME_HEADER = 'header';
    const NAME_FOOTER = 'footer';
    const NAME_STYLES = 'styles';

    const FILE_TYPE_JS = 'js';
    const FILE_TYPE_STYLE = 'css';
    const FILE_TYPE_TWIG = 'twig';

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
                    self::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
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
        return '{{%page_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content', 'json', 'json_draft', 'file_type'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'content' => Yii::t('app', 'Content'),
            'json' => Yii::t('app', 'Json'),
            'json_draft' => Yii::t('app', 'Json draft'),
            'file_type' => Yii::t('app', 'file_type'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
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
}
