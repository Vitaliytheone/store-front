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
    const TYPE_CREATE_STORE_EXPIRY = 23;
    const TYPE_CHANGE_STORE_EXPIRY = 24;
    const TYPE_CREATE_GATEWAY_EXPIRY = 25;
    const TYPE_CHANGE_GATEWAY_EXPIRY = 26;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.expired_log';
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
     * Get type by code method value
     * @param string $method
     * @return int|null
     */
    public static function getTypeByCode($method)
    {
        $type = 0;

        switch ($method) {
            case Params::CODE_PAYPAL:
                $type = ExpiredLog::TYPE_PAYPAL;
                break;

            case Params::CODE_WEBMONEY:
                $type = ExpiredLog::TYPE_WEBMONEY;
                break;

            case Params::CODE_PERFECT_MONEY:
                $type = ExpiredLog::TYPE_PERFECT_MONEY;
                break;

            case Params::CODE_BITCOIN:
                $type = ExpiredLog::TYPE_BITCOIN;
                break;

            case Params::CODE_TWO_CHECKOUT:
                $type = ExpiredLog::TYPE_TWO_CHECKOUT;
                break;
        }

        return $type;
    }
}
