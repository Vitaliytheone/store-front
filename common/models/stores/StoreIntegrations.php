<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "store_integrations".
 *
 * @property int $id
 * @property int $integration_id
 * @property int $store_id
 * @property string $options
 * @property int $visibility 1- активна, 0 - не активна
 * @property int $position
 * @property int $created_at
 * @property int $updated_at
 */
class StoreIntegrations extends ActiveRecord
{
    public const VISIBILITY_ON = 1;
    public const VISIBILITY_OFF = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_integrations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['integration_id', 'store_id', 'position', 'created_at', 'updated_at'], 'integer'],
            [['options'], 'string'],
            [['visibility'], 'string', 'max' => 1],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
            [['integration_id'], 'exist', 'skipOnError' => true, 'targetClass' => Integrations::class, 'targetAttribute' => ['integration_id' => 'id']],
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
                    if (isset($this->position)) {
                        return $this->position;
                    }
                    return $this->getLastPosition() + 1;
                },
            ],
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIntegration()
    {
        return $this->hasOne(Integrations::class, ['id' => 'integration_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'integration_id' => Yii::t('app', 'Integration ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'options' => Yii::t('app', 'Options'),
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
        return static::find()->where(['store_id' => $this->store_id])->max('position') ?? 0;
    }

    /**
     * Get options
     * @return array
     */
    public function getOptions(): array
    {
        return isset($this->options) ? json_decode($this->options, true) : [];
    }

    /**
     * Set options
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = json_encode($options);
    }
}
