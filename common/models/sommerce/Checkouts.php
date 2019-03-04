<?php

namespace common\models\sommerce;

use common\components\behaviors\IpBehavior;
use common\models\sommerces\PaymentMethods;
use common\models\sommerces\PaymentMethodsCurrency;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\sommerce\queries\CheckoutsQuery;

/**
 * This is the model class for table "{{%checkouts}}".
 *
 * @property integer $id
 * @property string $customer
 * @property string $price
 * @property integer $status
 * @property string $method_status
 * @property integer $method_id
 * @property integer $currency_id
 * @property string $ip
 * @property string $details
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $currency
 * @property string $user_details
 * @property string $redirect_url
 *
 * @property Orders $order
 * @property Orders[] $orders
 * @property Payments $payment
 * @property Payments[] $payments
 * @property PaymentsLog[] $paymentsLogs
 * @property Suborders[] $suborders
 * @property PaymentMethods $payMethod
 * @property PaymentMethodsCurrency $payMethodCurrency
 */
class Checkouts extends ActiveRecord
{
    public const STATUS_PENDING = 0;
    public const STATUS_PAID = 1;
    public const STATUS_EXPIRED = 2;

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checkouts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'method_id', 'created_at', 'updated_at', 'currency_id'], 'integer'],
            [['price'], 'number'],
            [['redirect_url'], 'string', 'max' => 300],
            [['details', 'user_details'], 'string'],
            [['customer', 'method_status', 'ip'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => static::STATUS_PENDING],
            [['currency'], 'string', 'max' => 10],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethodsCurrency::class, 'targetAttribute' => ['currency_id' => 'id']],
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
            'currency_id' => Yii::t('app', 'Currency ID'),
            'ip' => Yii::t('app', 'Ip'),
            'details' => Yii::t('app', 'Details'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'currency' => Yii::t('app', 'Currency'),
            'redirect_url' => ii::t('app', 'Redirect Url'),
            'user_details' => Yii::t('app', 'User details'),
        ];
    }

    /**
     * Return all Orders by current checkout->id
     * @return \yii\db\ActiveQuery
     */
    public function getOrders(): ActiveQuery
    {
        return $this->hasMany(Orders::class, ['checkout_id' => 'id']);
    }

    /**
     * Return one Order by current checkout->id
     * @return \yii\db\ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Orders::class, ['checkout_id' => 'id']);
    }

    /**
     * Return one Payment by current checkout->id
     * @return \yii\db\ActiveQuery
     */
    public function getPayment(): ActiveQuery
    {
        return $this->hasOne(Payments::class, ['checkout_id' => 'id']);
    }

    /**
     * Return all Payments by current checkout->id
     * @return \yii\db\ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payments::class, ['checkout_id' => 'id']);
    }

    /**
     * Return all PaymentsLog by current checkout->id
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentsLogs(): ActiveQuery
    {
        return $this->hasMany(PaymentsLog::class, ['checkout_id' => 'id']);
    }

    /**
     * Return all Suborders by current checkout->id
     * @return \yii\db\ActiveQuery
     */
    public function getSuborders(): ActiveQuery
    {
        return $this->hasMany(Suborders::class, ['checkout_id' => 'id']);
    }

    /**
     * Return one PaymentMethods by current checkout->method_id
     * @return \yii\db\ActiveQuery
     */
    public function getPayMethod(): ActiveQuery
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * Return one PaymentMethodsCurrency by current checkout->currency_id
     * @return \yii\db\ActiveQuery
     */
    public function getPayMethodCurrency(): ActiveQuery
    {
        return $this->hasOne(PaymentMethodsCurrency::class, ['id' => 'currency_id']);
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
            'ip' => [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ],
                'defaultValue' => ' '
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
     * Get details as array
     * @return array
     */
    public function getDetails(): array
    {
        return !empty($this->details) ? json_decode($this->details, true) : [];
    }

    /**
     * Get user details as array
     * @return array
     */
    public function getUserDetails(): array
    {
        return !empty($this->user_details) ? json_decode($this->user_details, true) : [];
    }

    /**
     * Set user details
     * @param $userDetails
     */
    public function setUserDetails($userDetails)
    {
        $this->user_details = !empty($userDetails) ? json_encode($userDetails) : null;
    }
}
