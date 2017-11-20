<?php

namespace common\models\stores;

use Yii;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property integer $id
 * @property integer $store_id
 * @property string $method
 * @property string $details
 * @property integer $active
 *
 * @property Stores $store
 */
class PaymentMethods extends \yii\db\ActiveRecord
{
    /* Payment methods names */
    const METHOD_PAYPAL = 'paypal';
    const METHOD_2CHECKOUT = '2checkout';
    const METHOD_BITCOIN = 'bitcoin';

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
            [['store_id', 'active'], 'integer'],
            [['details'], 'string'],
            [['method'], 'string', 'max' => 255],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'store_id' => Yii::t('app', 'Store ID'),
            'method' => Yii::t('app', 'Method'),
            'details' => Yii::t('app', 'Details'),
            'active' => Yii::t('app', '0 - disabled, 1 - enabled'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::className(), ['id' => 'store_id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\stores\queries\PaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\stores\queries\PaymentMethodsQuery(get_called_class());
    }
}
