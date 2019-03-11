<?php

namespace common\models\panel;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panel\queries\LanguagesQuery;

/**
 * This is the model class for table "languages".
 *
 * @property string $code
 * @property string $name
 * @property int $rtl (1- enabled, 0 - disabled)
 * @property int $active (1- active, 0 - not active)
 * @property int $default (1- enabled, 0 - disabled)
 * @property int $position
 * @property int $updated_at
 * @property int $created_at
 */
class Languages extends ActiveRecord
{
    const DIRECTION_LEFT = 0;
    const DIRECTION_RIGHT = 1;

    const VISIBILITY_ON = 1;
    const VISIBILITY_OFF = 0;

    static $languages = null;

    /** @inheritdoc */
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
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'languages';
    }

    /**
     * @return array
     */
    public static function getLanguages()
    {
        if (static::$languages != null) {
            return static::$languages;
        }

        static::$languages = static::find()
            ->select([
                'code',
                'name',
            ])
            ->from('languages')
            ->andWhere([
                'active' => 1,
            ])
            ->orderBy([
                'position' => SORT_ASC
            ])
            ->indexBy('code')
            ->all();

        return static::$languages;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name', 'position'], 'required'],
            [['rtl', 'active', 'default', 'position', 'updated_at', 'created_at'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 64],
            [['code'], 'unique'],
        ];
    }

    /**
     * Get language name uses config file
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return direction text code
     * @return string
     */
    public function getDirection()
    {
        return (int)$this->rtl === self::DIRECTION_LEFT ? 'ltr' : 'rtl';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'rtl' => Yii::t('app', 'Rtl'),
            'active' => Yii::t('app', 'Active'),
            'default' => Yii::t('app', 'Default'),
            'position' => Yii::t('app', 'Position'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At')
        ];
    }

    /**
     * {@inheritdoc}
     * @return LanguagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LanguagesQuery(get_called_class());
    }
}
