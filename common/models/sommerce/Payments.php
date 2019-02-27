<?php

namespace common\models\sommerce;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\sommerce\queries\PaymentsQuery;

/**
 * This is the model class for table "{{%payments}}".
 *
 * @property integer $id
 * @property integer $checkout_id
 * @property string $method
 * @property string $customer
 * @property string $amount
 * @property integer $status 1 - Awaiting, 2 - Completed, 3 - Failed, 4 - Refunded
 * @property string $fee
 * @property string $transaction_id
 * @property string $memo
 * @property string $response_status
 * @property string $name
 * @property string $email
 * @property string $country
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $currency
 *
 * @property Checkouts $checkout
 * @property Orders $order
 */
class Payments extends ActiveRecord
{
    const STATUS_AWAITING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;
    const STATUS_REFUNDED = 4;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'checkout_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'fee'], 'number'],
            [['method', 'customer', 'transaction_id', 'memo', 'response_status', 'name', 'email', 'country'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 10],
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::class, 'targetAttribute' => ['checkout_id' => 'id']],
            [['status'], 'default', 'value' => static::STATUS_AWAITING],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'checkout_id' => Yii::t('app', 'Checkout ID'),
            'method' => Yii::t('app', 'Payment method'),
            'customer' => Yii::t('app', 'Customer'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'fee' => Yii::t('app', 'Fee'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'memo' => Yii::t('app', 'Memo'),
            'response_status' => Yii::t('app', 'Response status'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'country' => Yii::t('app', 'Country'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'currency' => Yii::t('app', 'Currency'),
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
    public function getOrder()
    {
        return $this->hasOne(Orders::class, ['checkout_id' => 'id'])->via('checkout');
    }

    /**
     * @inheritdoc
     * @return PaymentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentsQuery(get_called_class());
    }

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
                        'updated_at'
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
     * Return status names list
     * @return array
     */
    public static function getStatusNames()
    {
        $names = [
            self::STATUS_AWAITING => Yii::t('admin', 'payments.status_awaiting'),
            self::STATUS_COMPLETED => Yii::t('admin', 'payments.status_completed'),
            self::STATUS_FAILED => Yii::t('admin', 'payments.status_failed'),
            self::STATUS_REFUNDED => Yii::t('admin', 'payments.status_refunded'),
        ];

        return $names;
    }

    /**
     * Return status name by status value
     * @param $status
     * @return mixed
     */
    public static function getStatusName($status)
    {
        return ArrayHelper::getValue(static::getStatusNames(), $status, $status);
    }
}
