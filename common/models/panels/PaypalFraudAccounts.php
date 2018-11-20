<?php

namespace common\models\panels;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "paypal_fraud_accounts".
 *
 * @property int $id
 * @property string $payer_id PayPal payer ID
 * @property string $payer_email PayPal payer email
 * @property int $fraud_risk 1 - high, 2 - critical
 * @property int $payer_status 1 - verified, 0 - unverified
 * @property int $created_at
 * @property int $updated_at
 */
class PaypalFraudAccounts extends \yii\db\ActiveRecord
{
    public const RISK_HIGH = 1;
    public const RISK_CRITICAL = 2;

    public const STATUS_VERIFIED = 1;
    public const STATUS_UNVERIFIED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paypal_fraud_accounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fraud_risk', 'payer_status', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['payer_id', 'payer_email'], 'string', 'max' => 1000],
            [['fraud_risk', 'payer_status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payer_id' => 'Payer ID',
            'payer_email' => 'Payer Email',
            'fraud_risk' => 'Fraud Risk',
            'payer_status' => 'Payer Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get risk list
     * @return array
     */
    public static function getRisks(): array
    {
        return [
            static::RISK_HIGH => Yii::t('app/superadmin', 'paypal_fraud_accounts.risk.high'),
            static::RISK_CRITICAL => Yii::t('app/superadmin', 'paypal_fraud_accounts.risk.critical'),
        ];
    }

    /**
     * Get status list
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            static::STATUS_UNVERIFIED => Yii::t('app/superadmin', 'paypal_fraud_accounts.status.unverified'),
            static::STATUS_VERIFIED => Yii::t('app/superadmin', 'paypal_fraud_accounts.status.verified'),
        ];
    }

    /**
     * Get risk name
     * @param int $risk
     * @return string
     */
    public static function getRiskName(int $risk): string
    {
        return ArrayHelper::getValue(static::getRisks(), $risk, '');
    }

    /**
     * Get status name
     * @param int $status
     * @return string
     */
    public static function getStatusName(int $status): string
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }
}
