<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "expired_log".
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $expired_last
 * @property integer $expired
 * @property integer $created_at
 * @property integer $type
 */
class ExpiredLog extends ActiveRecord
{
    const TYPE_PAYPAL = 1;
    const TYPE_PERFECT_MONEY = 3;
    const TYPE_WEBMONEY = 2;
    const TYPE_TWO_CHECKOUT = 20;
    const TYPE_BITCOIN = 4;
    const TYPE_CHANGE_EXPIRY = 21;
    const TYPE_CREATE_EXPIRY = 22;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expired_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'expired', 'created_at', 'type'], 'required'],
            [['pid', 'expired_last', 'expired', 'created_at', 'type'], 'integer'],
            [['type'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    /**
     * Get type by gateway method value
     * @param integer $method
     * @return int|null
     */
    public static function getTypeByGateway($method)
    {
        $type = 0;

        switch ($method) {
            case PaymentGateway::METHOD_PAYPAL:
                $type = ExpiredLog::TYPE_PAYPAL;
                break;

            case PaymentGateway::METHOD_WEBMONEY:
                $type = ExpiredLog::TYPE_WEBMONEY;
                break;

            case PaymentGateway::METHOD_PERFECT_MONEY:
                $type = ExpiredLog::TYPE_PERFECT_MONEY;
                break;

            case PaymentGateway::METHOD_BITCOIN:
                $type = ExpiredLog::TYPE_BITCOIN;
                break;

            case PaymentGateway::METHOD_TWO_CHECKOUT:
                $type = ExpiredLog::TYPE_TWO_CHECKOUT;
                break;
        }

        return $type;
    }
}
