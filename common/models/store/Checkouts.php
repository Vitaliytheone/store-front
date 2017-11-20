<?php

namespace common\models\store;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\CheckoutsQuery;

/**
 * This is the model class for table "{{%checkouts}}".
 *
 * @property integer $id
 * @property string $customer
 * @property string $price
 * @property integer $status
 * @property string $method_status
 * @property integer $method_id
 * @property string $ip
 * @property string $details
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $currency
 *
 * @property Orders[] $orders
 * @property Payments[] $payments
 * @property PaymentsLog[] $paymentsLogs
 * @property Suborders[] $suborders
 */
class Checkouts extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%checkouts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'method_id', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['details'], 'string'],
            [['customer', 'method_status', 'ip'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => static::STATUS_PENDING],
            [['currency'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer' => Yii::t('app', 'Customer'),
            'price' => Yii::t('app', 'Price'),
            'status' => Yii::t('app', 'Status'),
            'method_status' => Yii::t('app', 'Method Status'),
            'method_id' => Yii::t('app', 'Method ID'),
            'ip' => Yii::t('app', 'Ip'),
            'details' => Yii::t('app', 'Details'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'currency' => Yii::t('app', 'Currency'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['checkout_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::className(), ['checkout_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentsLogs()
    {
        return $this->hasMany(PaymentsLog::className(), ['checkout_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuborders()
    {
        return $this->hasMany(Suborders::className(), ['checkout_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return CheckoutsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CheckoutsQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * Set details
     * @param array $details
     */
    public function setDetails(array $details)
    {
        $this->details = json_encode($details);
    }

    /**
     * Get details
     */
    public function getDetails()
    {
        return empty($this->details) ? [] : json_decode($this->details, true);
    }
}
