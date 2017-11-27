<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use common\models\stores\queries\PaymentMethodsQuery;
use yii\helpers\ArrayHelper;

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
class PaymentMethods extends ActiveRecord
{
    /* Payment methods names */
    const METHOD_PAYPAL = 'paypal';
    const METHOD_2CHECKOUT = '2checkout';
    const METHOD_BITCOIN = 'bitcoin';

    const ACTIVE_DISABLED = 0;
    const ACTIVE_ENABLED = 1;

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
            'active' => Yii::t('app', 'Active'),
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
     * @return PaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentMethodsQuery(get_called_class());
    }

    /**
     * Get available payment method names
     * @return array
     */
    public static function getNames()
    {
        return [
            static::METHOD_PAYPAL => 'PayPal',
            static::METHOD_2CHECKOUT => '2Checkout',
            static::METHOD_BITCOIN => 'Bitcoin',
        ];
    }

    /**
     * Get payment method name
     * @return string
     */
    public function getName()
    {
        return ArrayHelper::getValue(static::getNames(), $this->method, '');
    }

    /**
     * Get payment method details
     * @return array|mixed
     */
    public function getDetails()
    {
        return !empty($this->details) ? json_decode($this->details, true) : [];
    }

    /**
     * Return payment method title by method
     * @param $method
     * @return mixed
     */
    public static function getMethodTitle($method)
    {
        $titles = [
            self::METHOD_PAYPAL => Yii::t('admin', 'payments.payment_method_paypal'),
            self::METHOD_2CHECKOUT => Yii::t('admin', 'payments.payment_method_2checkout'),
            self::METHOD_BITCOIN => Yii::t('admin', 'payments.payment_method_bitcoin'),
        ];

        return ArrayHelper::getValue($titles, $method, $method);
    }

}
