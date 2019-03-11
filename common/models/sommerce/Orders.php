<?php

namespace common\models\sommerce;

use common\components\behaviors\IpBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerce\queries\OrdersQuery;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property integer $id
 * @property string $code
 * @property integer $checkout_id
 * @property string $customer
 * @property integer $in_progress
 * @property integer $created_at
 *
 * @property Checkouts $checkout
 * @property Payments $payment
 * @property Suborders[] $suborders
 */
class Orders extends ActiveRecord
{
    const IN_PROGRESS_ENABLED = 1;
    const IN_PROGRESS_DISABLED = 0;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'checkout_id', 'created_at', 'in_progress'], 'integer'],
            [['in_progress'], 'default', 'value' => static::IN_PROGRESS_DISABLED],
            [['code'], 'string', 'max' => 64],
            [['customer'], 'string', 'max' => 255],
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::class, 'targetAttribute' => ['checkout_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'orders.f_id'),
            'code' => Yii::t('admin', 'orders.f_code'),
            'checkout_id' => Yii::t('admin', 'orders.f_checkout_id'),
            'customer' => Yii::t('admin', 'orders.f_customer'),
            'created_at' => Yii::t('admin', 'orders.f_created_at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckout()
    {
        return $this->hasOne(Checkouts::class, ['id' => 'checkout_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuborders()
    {
        return $this->hasMany(Suborders::class, ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payments::class, ['checkout_id' => 'id'])->via('checkout');
    }

    /**
     * @inheritdoc
     * @return OrdersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrdersQuery(get_called_class());
    }

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
            'code' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'code',
                ],
                'value' => function() {
                    return static::generateCodeString();
                },
            ]
        ];
    }

    /**
     * @return string
     */
    public static function generateCodeString()
    {
        return md5(bin2hex(random_bytes(32))) . md5(bin2hex(random_bytes(32)));
    }
}
