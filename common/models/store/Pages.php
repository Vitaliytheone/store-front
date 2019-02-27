<?php

namespace common\models\store;

use common\models\store\queries\PagesQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%pages}}".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property int $visibility
 * @property int $is_draft
 * @property string $twig editor twig source
 * @property string $json editor published json
 * @property string $json_draft editor unpublished json
 * @property int $created_at
 * @property int $updated_at
 * @property int $publish_at
 */
class Pages extends ActiveRecord
{
    const VISIBILITY_ON = 1;
    const VISIBILITY_OFF = 0;

    const IS_DRAFT_ON = 1;
    const IS_DRAFT_OFF = 0;

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
            [['twig', 'json', 'json_draft'], 'string'],
            [['visibility', 'is_draft', 'created_at', 'updated_at', 'publish_at'], 'integer'],
            [['url', 'seo_title', 'name'], 'string', 'max' => 300],
            [['seo_description', 'seo_keywords'], 'string', 'max' => 2000],
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
            'url' => Yii::t('app', 'Url'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'seo_keywords' => Yii::t('app', 'Seo Keywords'),
            'visibility' => Yii::t('app', 'Visibility'),
            'is_draft' => Yii::t('app', 'Is Draft'),
            'twig' => Yii::t('app', 'Editor twig source'),
            'json' => Yii::t('app', 'Editor published json'),
            'json_draft' => Yii::t('app', 'Editor draft json'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'publish_at' => Yii::t('app', 'Publish'),
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

    /**
     * Set json_draft field
     * @param array $json
     */
    public function setJsonDraft($json)
    {
        $this->json_draft = json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Get json_draft field
     * @return array|mixed
     */
    public function getJsonDraft()
    {
        return json_decode($this->json_draft, true);
    }

    /**
     * Set json_draft field
     * @param array $json
     */
    public function setJson($json)
    {
        $this->json = json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Get json_draft field
     * @return array|mixed
     */
    public function getJson()
    {
        return json_decode($this->json, true);
    }

}
