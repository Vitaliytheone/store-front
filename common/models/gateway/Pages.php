<?php

namespace common\models\gateway;

use Yii;
use yii\db\ActiveRecord;
use common\models\gateway\queries\PagesQuery;

/**
 * This is the model class for table "{{%pages}}".
 *
 * @property int $id
 * @property string $title
 * @property int $visibility
 * @property string $content
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property string $url
 * @property string $template_name
 * @property int $deleted
 * @property int $is_default
 * @property int $created_at
 * @property int $updated_at
 */
class Pages extends ActiveRecord
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
        return '{{%pages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['title', 'seo_title', 'url'], 'string', 'max' => 255],
            [['visibility', 'deleted', 'is_default'], 'string', 'max' => 1],
            [['seo_description', 'seo_keywords'], 'string', 'max' => 2000],
            [['template_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'visibility' => Yii::t('app', 'Visibility'),
            'content' => Yii::t('app', 'Content'),
            'seo_title' => Yii::t('app', 'Seo Title'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'seo_keywords' => Yii::t('app', 'Seo Keywords'),
            'url' => Yii::t('app', 'Url'),
            'template_name' => Yii::t('app', 'Template Name'),
            'deleted' => Yii::t('app', 'Deleted'),
            'is_default' => Yii::t('app', 'Is Default'),
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