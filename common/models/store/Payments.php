<?php

namespace common\models\store;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\PaymentsQuery;

/**
 * This is the model class for table "{{%payments}}".
 *
 * @property integer $id
 * @property integer $checkout_id
 * @property string $method
 * @property string $customer
 * @property string $amount
 * @property integer $status
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
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::className(), 'targetAttribute' => ['checkout_id' => 'id']],
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
        return $this->hasOne(Checkouts::className(), ['id' => 'checkout_id']);
    }

    /**
     * @inheritdoc
     * @return PaymentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentsQuery(get_called_class());
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
}
