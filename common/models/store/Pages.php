<?php

namespace common\models\store;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\PagesQuery;

/**
 * This is the model class for table "{{%pages}}".
 *
 * @property int $id
 * @property string $url
 * @property string $title
 * @property int $visibility
 * @property string $twig editor twig source
 * @property string $styles editor styles source
 * @property string $json editor published json
 * @property string $json_dev editor unpublished json
 * @property int $created_at
 * @property int $updated_at
 */
class Pages extends ActiveRecord
{
    const VISIBILITY_ON = 1;
    const VISIBILITY_OFF = 0;

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
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages}}';
    }

    /**
     * @return mixed
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
            [['twig', 'styles', 'json', 'json_dev'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['url', 'title'], 'string', 'max' => 300],
            [['visibility'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'title' => Yii::t('app', 'Title'),
            'visibility' => Yii::t('app', 'Visibility'),
            'twig' => Yii::t('app', 'editor twig source'),
            'styles' => Yii::t('app', 'editor styles source'),
            'json' => Yii::t('app', 'editor published json'),
            'json_dev' => Yii::t('app', 'editor unpublished json'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return PagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PagesQuery(get_called_class());
    }

}
