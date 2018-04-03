<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\NotificationsQuery;

/**
 * This is the model class for table "{{%notifications}}".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $item
 * @property string $type
 * @property string $response
 * @property integer $date
 */
class Notifications extends ActiveRecord
{
    const ITEM_PANEL = 1;
    const ITEM_SSL = 2;
    const ITEM_DOMAIN = 3;
    const ITEM_CUSTOMER = 4;
    const ITEM_PAYMENTS = 5;
    const ITEM_TICKET = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'response'], 'required'],
            [['item_id', 'item', 'date'], 'integer'],
            [['response'], 'string'],
            [['type'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'item_id' => Yii::t('app', 'Item ID'),
            'item' => Yii::t('app', 'Item'),
            'type' => Yii::t('app', 'Type'),
            'response' => Yii::t('app', 'Response'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @inheritdoc
     * @return NotificationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationsQuery(get_called_class());
    }

    /**
     * Get items
     * @return array
     */
    public static function getItems()
    {
        return [
            static::ITEM_PANEL => Yii::t('app', 'notifications.item.panel'),
            static::ITEM_SSL => Yii::t('app', 'notifications.item.ssl'),
            static::ITEM_DOMAIN => Yii::t('app', 'notifications.item.domain'),
            static::ITEM_CUSTOMER => Yii::t('app', 'notifications.item.customer'),
            static::ITEM_PAYMENTS => Yii::t('app', 'notifications.item.payments'),
            static::ITEM_TICKET => Yii::t('app', 'notifications.item.ticket'),
        ];
    }

    /**
     * Get item name
     * @return string
     */
    public function getItemName()
    {
        return static::getItems()[$this->item];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}
