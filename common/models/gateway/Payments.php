<?php

namespace common\models\gateway;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateway\queries\PaymentsQuery;

/**
 * This is the model class for table "{{%payments}}".
 *
 * @property int $id
 * @property int $source_type 1 - panel; 2- store
 * @property int $source_id
 * @property int $source_payment_id
 * @property int $method_id
 * @property string $currency
 * @property string $amount
 * @property int $status 0 - pending; 1 - completed; 2 - expired; 3 - writing; 4 - fail; 5 - hold
 * @property string $transaction_id
 * @property string $response_status
 * @property string $response
 * @property string $success_url
 * @property string $fail_url
 * @property string $return_url
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PaymentsLog[] $paymentsLogs
 */
class Payments extends ActiveRecord
{
    public const STATUS_PENDING = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_EXPIRED = 2;
    public const STATUS_WAITING = 3;
    public const STATUS_FAIL = 4;
    public const STATUS_HOLD = 5;

    public const SOURCE_TYPE_PANEL = 1;
    public const SOURCE_TYPE_STORE = 2;

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
            [['source_type', 'source_id', 'source_payment_id', 'method_id', 'currency', 'amount'], 'required'],
            [['source_id', 'source_payment_id', 'method_id', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['source_type', 'status'], 'string', 'max' => 1],
            [['currency'], 'string', 'max' => 3],
            [['response_status', 'success_url', 'fail_url', 'return_url', 'transaction_id'], 'string', 'max' => 300],
            [['response'], 'string', 'max' => 1000],
            [['status'], 'default', 'value' => static::STATUS_PENDING],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'source_type' => Yii::t('app', 'Source'),
            'source_id' => Yii::t('app', 'Source ID'),
            'source_payment_id' => Yii::t('app', 'Source Payment ID'),
            'method_id' => Yii::t('app', 'Method ID'),
            'currency' => Yii::t('app', 'Currency'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'response_status' => Yii::t('app', 'Response Status'),
            'response' => Yii::t('app', 'Response'),
            'success_url' => Yii::t('app', 'Success Url'),
            'fail_url' => Yii::t('app', 'Fail Url'),
            'return_url' => Yii::t('app', 'Return Url'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentsLogs()
    {
        return $this->hasMany(PaymentsLog::class, ['payment_id' => 'id']);
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
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_PENDING => Yii::t('app', 'payments.status.pending'),
            static::STATUS_COMPLETED => Yii::t('app', 'payments.status.completed'),
            static::STATUS_EXPIRED => Yii::t('app', 'payments.status.expired'),
            static::STATUS_WAITING => Yii::t('app', 'payments.status.waiting'),
            static::STATUS_FAIL => Yii::t('app', 'payments.status.fail'),
            static::STATUS_HOLD => Yii::t('app', 'payments.status.hold'),
        ];
    }

    /**
     * @return array
     */
    public static function getSourceTypes()
    {
        return [
            static::SOURCE_TYPE_PANEL => Yii::t('app', 'payments.source_type.panel'),
            static::SOURCE_TYPE_STORE => Yii::t('app', 'payments.source_type.store'),
        ];
    }
}