<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\ReferralEarningsQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%referral_earnings}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $earnings
 * @property integer $invoice_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Invoices $invoice
 */
class ReferralEarnings extends ActiveRecord
{
    const STATUS_COMPLETED = 1;
    const STATUS_REJECTED = 2;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.referral_earnings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'earnings', 'invoice_id', 'created_at'], 'required'],
            [['customer_id', 'invoice_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['earnings'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'earnings' => Yii::t('app', 'Earnings'),
            'invoice_id' => Yii::t('app', 'Invoice ID'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ReferralEarningsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReferralEarningsQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::class, ['id' => 'invoice_id']);
    }

    public static function getStatuses()
    {
        return [
            static::STATUS_COMPLETED => Yii::t('app', 'referral_earnings.status.completed'),
            static::STATUS_REJECTED => Yii::t('app', 'referral_earnings.status.rejected'),
        ];
    }

    /**
     * Get status name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatusNameString($this->status);
    }

    /**
     * Get status name
     * @param integer $status
     * @return string
     */
    public static function getStatusNameString($status)
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ]
        ];
    }
}
