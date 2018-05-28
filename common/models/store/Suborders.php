<?php
namespace common\models\store;

use common\models\stores\Providers;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;
use common\models\store\queries\SubordersQuery;

/**
 * This is the model class for table "{{%suborders}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $checkout_id
 * @property string $link
 * @property string $currency
 * @property string $amount
 * @property integer $package_id
 * @property integer $quantity
 * @property integer $status
 * @property integer $updated_at
 * @property integer $mode
 * @property integer $send
 * @property integer $provider_id
 * @property string $provider_service
 * @property string $provider_order_id
 * @property string $provider_charge
 * @property string $provider_response
 *
 * @property Checkouts $checkout
 * @property Orders $order
 * @property Packages $package
 * @property Providers $provider
 */
class Suborders extends ActiveRecord
{
    /* Suborder status constants */
    const STATUS_AWAITING       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_IN_PROGRESS    = 3;
    const STATUS_COMPLETED      = 4;
    const STATUS_CANCELED       = 5;
    const STATUS_FAILED         = 6;
    const STATUS_ERROR          = 7;

    /* Suborder mode constants */
    const MODE_MANUAL           = 0;
    const MODE_AUTO             = 1;

    /* Suborder resend status */
    const SEND_STATUS_AWAITING  = 1; // заказ ждет отправки
    const SEND_STATUS_SENDING   = 2; // заказ отправляется
    const SEND_STATUS_SENT      = 3; // заказ отправлен

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'updated_at',
                    self::EVENT_BEFORE_UPDATE => 'updated_at',

                ],
                'value' => function ($event) {
                    return time();
                },
            ],
        ];
    }

    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%suborders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'checkout_id', 'package_id', 'quantity', 'status', 'updated_at', 'mode', 'send', 'provider_id'], 'integer'],
            [['amount', 'provider_charge'], 'number'],
            [['provider_response'], 'string'],
            [['link'], 'string', 'max' => 1000],
            [['provider_service', 'provider_order_id'], 'string', 'max' => 300],
            [['status'], 'default', 'value' => static::STATUS_AWAITING],
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::class, 'targetAttribute' => ['checkout_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['order_id' => 'id']],
            [['package_id'], 'exist', 'skipOnError' => true, 'targetClass' => Packages::class, 'targetAttribute' => ['package_id' => 'id']],
            [['currency'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('admin', 'sorders.f_id'),
            'order_id' => Yii::t('admin', 'sorders.f_order_id'),
            'checkout_id' => Yii::t('admin', 'sorders.f_checkout_id'),
            'link' => Yii::t('admin', 'sorders.f_link'),
            'currency' => Yii::t('app', 'Currency'),
            'amount' => Yii::t('admin', 'sorders.f_amount'),
            'package_id' => Yii::t('admin', 'sorders.f_package_id'),
            'quantity' => Yii::t('admin', 'sorders.f_quantity'),
            'status' => Yii::t('admin', 'sorders.f_status'),
            'updated_at' => Yii::t('admin', 'sorders.f_updated_at'),
            'mode' => Yii::t('admin', 'sorders.f_mode'),
            'provider_id' => Yii::t('admin', 'sorders.f_provider_id'),
            'provider_service' => Yii::t('admin', 'sorders.f_provider_service'),
            'provider_order_id' => Yii::t('admin', 'sorders.f_provider_order_id'),
            'provider_charge' => Yii::t('admin', 'sorders.f_provider_charge'),
            'provider_response' => Yii::t('admin', 'sorders.f_provider_response'),
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
        return $this->hasOne(Orders::class, ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackage()
    {
        return $this->hasOne(Packages::class, ['id' => 'package_id']);
    }

    /**
     * Return provider by current suborder provider_id
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Providers::class, ['id' => 'provider_id']);
    }

    /**
     * @inheritdoc
     * @return SubordersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubordersQuery(get_called_class());
    }

    /**
     * Return status names list
     * @return array
     */
    public static function getStatusNames()
    {
        $names = [
            self::STATUS_AWAITING => Yii::t('admin', 'orders.filter_status_awaiting'),
            self::STATUS_PENDING => Yii::t('admin', 'orders.filter_status_pending'),
            self::STATUS_IN_PROGRESS => Yii::t('admin', 'orders.filter_status_in_progress'),
            self::STATUS_COMPLETED => Yii::t('admin', 'orders.filter_status_completed'),
            self::STATUS_CANCELED => Yii::t('admin', 'orders.filter_status_canceled'),
            self::STATUS_FAILED => Yii::t('admin', 'orders.filter_status_failed'),
            self::STATUS_ERROR => Yii::t('admin', 'orders.filter_status_error'),
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

    /**
     * Return mode names list
     * @return array
     */
    public static function getModeNames()
    {
        $names = [
            self::MODE_MANUAL => Yii::t('admin', 'orders.filter_mode_manual'),
            self::MODE_AUTO => Yii::t('admin', 'orders.filter_mode_auto'),
        ];

        return $names;
    }

    /**
     * Return mode name by mode value
     * @param $mode
     * @return mixed
     */
    public static function getModeName($mode)
    {
        return ArrayHelper::getValue(static::getModeNames(), $mode, $mode);
    }

    /**
     * Change suborder status
     * @param $status
     * @param int $mode
     * @return bool
     */
    public function changeStatus($status, $mode = self::MODE_MANUAL)
    {
        $this->setAttributes([
            'status' => $status,
            'mode' => $mode,
        ]);

        return $this->save();
    }

    /**
     * Cancel suborder
     * @return bool
     */
    public function cancel()
    {
        $this->setAttribute('status', self::STATUS_CANCELED);

        return $this->save();
    }

    /**
     * Resend suborder
     * @return bool
     */
    public function resend()
    {
        $this->setAttributes([
            'status' => self::STATUS_AWAITING,
            'send' => self::SEND_STATUS_AWAITING,
        ]);

        return $this->save();
    }
}
