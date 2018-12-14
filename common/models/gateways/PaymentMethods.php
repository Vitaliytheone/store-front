<?php

namespace common\models\gateways;

use Yii;
use yii\db\ActiveRecord;
use common\models\gateways\queries\PaymentMethodsQuery;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property int $id
 * @property string $method_name
 *
 * @property SitePaymentMethods[] $sitePaymentMethods
 */
class PaymentMethods extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment_methods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['method_name'], 'required'],
            [['method_name'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'method_name' => Yii::t('app', 'Method Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSitePaymentMethods()
    {
        return $this->hasMany(SitePaymentMethods::class, ['method_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return PaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentMethodsQuery(get_called_class());
    }
}