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
    const METHOD_PAYPAL_STANDARD = 'paypalstandard';
    const METHOD_2CHECKOUT = '2checkout';
    const METHOD_COINPAYMENTS = 'coinpayments';
    const METHOD_PAGSEGURO = 'pagseguro';
    const METHOD_WEBMONEY = 'webmoney';
    const METHOD_YANDEX_MONEY = 'yandexmoney';
    const METHOD_YANDEX_CARDS = 'yandexcards';
    const METHOD_FREE_KASSA = 'freekassa';
    const METHOD_PAYTR = 'paytr';
    const METHOD_PAYWANT = 'paywant';
    const METHOD_BILLPLZ = 'billplz';
    const METHOD_AUTHORIZE = 'authorize';
    const METHOD_STRIPE = 'stripe';
    const METHOD_MERCADOPAGO = 'mercadopago';

    const ACTIVE_DISABLED = 0;
    const ACTIVE_ENABLED = 1;

    public static $methodsNames = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.payment_methods';
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
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
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
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
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
        if (empty(static::$methodsNames) || !is_array(static::$methodsNames)) {
            static::$methodsNames = PaymentGateways::find()
                ->select(['name'])
                ->indexBy('method')
                ->asArray()
                ->column();
        }

        return static::$methodsNames;
    }

    /**
     * Get payment method name
     * @return string
     */
    public function getName()
    {
        return ArrayHelper::getValue(static::getNames(), $this->method, $this->method);
    }

    /**
     * Return payment method title by method
     * @param $method
     * @return mixed
     */
    public static function getMethodName($method)
    {
        return ArrayHelper::getValue(static::getNames(), $method, $method);
    }

    /**
     * Get payment method details
     * @return array|mixed
     */
    public function getDetails()
    {
        return !empty($this->details) ? json_decode($this->details, true) : [];
    }
}
