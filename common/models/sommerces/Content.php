<?php

namespace common\models\sommerces;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerces\queries\ContentQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%content}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $text
 * @property integer $updated_at
 */
class Content extends ActiveRecord
{
    static $contents;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_SOMMERCES . '.content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['text'], 'string'],
            [['text'], 'default', 'value' => ''],
            [['updated_at'], 'integer'],
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
            'name' => Yii::t('app', 'Name'),
            'text' => Yii::t('app', 'Text'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ContentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContentQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Get contents
     * @return mixed
     */
    public static function getContents()
    {
        if (null !== static::$contents) {
            return static::$contents;
        }

        static::$contents = [];

        $contents = (new Query())
            ->select([
                'name',
                'text'
            ])
            ->from(static::tableName())
            ->all();

        static::$contents = ArrayHelper::map($contents, 'name', 'text');

        return static::$contents;
    }

    /**
     * Get content by name
     * Content string can contains replacement placeholders:
     * 'Hello, {username}!'
     *
     * @param string $name
     * @param $params array replacements key => value pairs
     * @return string|null
     */
    public static function getContent($name, $params = [])
    {
        $message = (string)ArrayHelper::getValue(static::getContents(), $name, '');

        if (empty($message) || empty($params)) {
            return $message;
        }

        $placeholders = [];
        foreach ((array) $params as $key => $value) {
            $placeholders['{{' . $key . '}}'] = $value;
        }

        return ($placeholders === []) ? $message : strtr($message, $placeholders);
    }
}
