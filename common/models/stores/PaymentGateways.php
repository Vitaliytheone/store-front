<?php

namespace common\models\stores;

use Yii;
use \yii\db\ActiveRecord;
use \common\models\stores\queries\PaymentGatewaysQuery;

/**
 * This is the model class for table "payment_gateways".
 *
 * @property integer $id
 * @property string $method
 * @property string $currencies
 */
class PaymentGateways extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.payment_gateways';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['method'], 'string', 'max' => 255],
            [['currencies'], 'string', 'max' => 3000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'method' => Yii::t('app', 'Method'),
            'currencies' => Yii::t('app', 'Currencies'),
        ];
    }

    /**
     * @inheritdoc
     * @return PaymentGatewaysQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentGatewaysQuery(get_called_class());
    }
}
