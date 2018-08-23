<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\LanguagesQuery;
use yii\db\Query;

/**
 * This is the model class for table "{{%languages}}".
 *
 * @property int $id
 * @property int $panel_id
 * @property string $language_code
 * @property string $name
 * @property int $position
 * @property int $direction
 * @property int $created_at
 * @property int $updated_at
 * @property int $visibility
 * @property int $edited
 */
class Languages extends ActiveRecord
{
    const DIRECTION_LEFT = 0;
    const DIRECTION_RIGHT = 1;

    const VISIBILITY_ON = 1;
    const VISIBILITY_OFF = 0;

    const EDITED_ON = 1;
    const EDITED_OFF = 0;

    private static $_languages = [];

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
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
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
        return '{{%languages}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'position', 'created_at', 'edited'], 'required'],
            [['panel_id', 'position', 'created_at', 'updated_at', 'visibility', 'edited', 'direction'], 'integer'],
            [['language_code'], 'string', 'max' => 10],
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
            'panel_id' => Yii::t('app', 'Panel ID'),
            'language_code' => Yii::t('app', 'Language Code'),
            'name' => Yii::t('app', 'Name'),
            'position' => Yii::t('app', 'Position'),
            'direction' => Yii::t('app', 'Direction'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'visibility' => Yii::t('app', 'Visibility'),
            'edited' => Yii::t('app', 'Edited'),
        ];
    }

    /**
     * @inheritdoc
     * @return LanguagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LanguagesQuery(get_called_class());
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
        return (int)$this->direction === self::DIRECTION_LEFT ? 'ltr' : 'rtl';
    }

    /**
     * Get panel languages
     * @param $pid
     * @return mixed
     */
    public static function getLanguages($pid)
    {
        if (!empty(static::$_languages[$pid])) {
            return static::$_languages[$pid];
        }

        static::$_languages[$pid] = [];

        $languages = (new Query())
            ->select([
                'id',
                'language_code',
                'name',
            ])
            ->from('languages')
            ->andWhere([
                'panel_id' => $pid,
                'visibility' => 1,
            ])
            ->orderBy([
                'position' => SORT_ASC
            ])
            ->all();

        foreach ($languages as $language) {
            static::$_languages[$pid][$language['language_code']] = $language['name'];
        }

        return static::$_languages[$pid];
    }
}
