<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "paypal_payments".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $user_id
 * @property int $payment_id Table `payments` ID
 * @property string $transaction_id PayPal transaction id
 * @property string $paypal_options Payment gateway data from payment_gateway table
 * @property string $payer_id PayPal payer ID
 * @property string $payer_email PayPal payer email
 * @property string $firstname PayPal payer firstname
 * @property string $lastname PayPal payer lastname
 * @property string $paypal_status PayPal payment status
 * @property int $payment_created_at PayPal payment created at
 * @property string $ip Panel customer IP address
 * @property string $amount Payment amount
 * @property string $response PayPal server response
 * @property int $created_at
 * @property int $updated_at
 * @property int $checked_at
 * @property string $reason Reason for payment checking
 */
class PaypalPayments extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paypal_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'user_id', 'payment_id', 'payment_created_at', 'created_at', 'updated_at', 'checked_at'], 'integer'],
            [['paypal_options', 'amount'], 'required'],
            [['paypal_options', 'response'], 'string'],
            [['amount'], 'number'],
            [['transaction_id', 'firstname', 'lastname', 'ip'], 'string', 'max' => 300],
            [['payer_id', 'payer_email', 'paypal_status', 'reason'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'panel_id' => 'Panel ID',
            'user_id' => 'User ID',
            'payment_id' => 'Payment ID',
            'transaction_id' => 'Transaction ID',
            'paypal_options' => 'Paypal Options',
            'payer_id' => 'Payer ID',
            'payer_email' => 'Payer Email',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'paypal_status' => 'Paypal Status',
            'payment_created_at' => 'Payment Created At',
            'ip' => 'Ip',
            'amount' => 'Amount',
            'response' => 'Response',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'checked_at' => 'Checked At',
            'reason' => 'Reason',
        ];
    }

    /**
     * {@inheritdoc}
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
}
