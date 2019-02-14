<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "integrations".
 *
 * @property int $id
 * @property string $category
 * @property string $code
 * @property string $name
 * @property string $widget_class
 * @property string $settings_form
 * @property string $settings_description
 * @property int $visibility 1- видема для всех, 0 - не видима для всех
 * @property int $position
 * @property int $created_at
 * @property int $updated_at
 */
class Integrations extends ActiveRecord
{
    public const CODE_CHAT_ZENDESK = 'zendesk';
    public const CODE_CHAT_JIVOCHAT = 'jivochat';
    public const CODE_CHAT_SMARTSUPP = 'smartsupp';

    public const CODE_ANALYTICS_GOOGLE = 'google_analytics';

    public const CATEGORY_CHATS = 'chats';
    public const CATEGORY_ANALYTICS = 'analytics';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'integrations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'code'], 'required'],
            [['settings_form', 'settings_description'], 'string'],
            [['position', 'created_at', 'updated_at'], 'integer'],
            [['category', 'code', 'name', 'widget_class'], 'string', 'max' => 255],
            [['visibility'], 'string', 'max' => 1],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
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
            'position' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'position',
                ],
                'value' => function ($event) {
                    return $this->getLastPosition() + 1;
                },
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'widget_class' => Yii::t('app', 'Widget Class'),
            'settings_form' => Yii::t('app', 'Settings Form'),
            'settings_description' => Yii::t('app', 'Settings Description'),
            'visibility' => Yii::t('app', 'Visibility'),
            'position' => Yii::t('app', 'Position'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Get last position
     * @return int
     */
    public function getLastPosition(): int
    {
        return static::find()->max('position') ?? 0;
    }

    /**
     * Get icons
     * @return array
     */
    public static function getIcons(): array
    {
        return [
            static::CODE_CHAT_ZENDESK => '/img/integrations/zendesk.png',
            static::CODE_CHAT_JIVOCHAT => '/img/integrations/jivochat.png',
            static::CODE_CHAT_SMARTSUPP => '/img/integrations/smartsup.png',
            static::CODE_ANALYTICS_GOOGLE => '/img/integrations/google_analitics.png',
        ];
    }

    /**
     * Get integration icon by code
     * @param string $code
     * @return string
     */
    public static function getIconByCode(string $code): string
    {
        return ArrayHelper::getValue(static::getIcons(), $code, '');
    }
}
