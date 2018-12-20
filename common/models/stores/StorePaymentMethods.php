<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use common\models\stores\queries\StorePaymentMethodsQuery;

/**
 * This is the model class for table "{{%store_payment_methods}}".
 *
 * @property int $id
 * @property int $store_id
 * @property string $options
 * @property int $visibility
 * @property int $method_id
 * @property int $currency_id
 * @property string $name
 * @property int $position
 * @property int $created_at
 * @property int $updated_at
 */
class StorePaymentMethods extends \yii\db\ActiveRecord
{
    const VISIBILITY_DISABLED = 0;
    const VISIBILITY_ENABLED = 1;

    public static $methodsNames = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_payment_methods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'store_id'], 'required'],
            [['id', 'store_id', 'method_id', 'currency_id', 'position', 'created_at', 'updated_at'], 'integer'],
            [['options'], 'string'],
            [['visibility'], 'string', 'max' => 1],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethodsCurrency::class, 'targetAttribute' => ['method_id' => 'id']],
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
            'options' => Yii::t('app', 'Options'),
            'visibility' => Yii::t('app', 'Visibility'),
            'method_id' => Yii::t('app', 'Method ID'),
            'currency_id' => Yii::t('app', 'Currency ID'),
            'name' => Yii::t('app', 'Name'),
            'position' => Yii::t('app', 'Position'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPaymentMethodCurrency()
    {
        return $this->hasOne(PaymentMethodsCurrency::class, ['id' => 'currency_id']);
    }

    /**
     * @inheritdoc
     * @return StorePaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StorePaymentMethodsQuery(get_called_class());
    }

    /**
     * Get available payment method names
     * @return array
     */
    public static function getNames(): array
    {
        if (empty(static::$methodsNames) || !is_array(static::$methodsNames)) {
            static::$methodsNames = PaymentGateways::find()
                ->select(['name'])
                ->indexBy('method_id')
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
        return ArrayHelper::getValue(static::getNames(), $this->method_id);
    }

    /**
     * Return payment method title by method
     * @param int $methodId
     * @return string|null
     */
    public static function getMethodName($methodId)
    {
        return ArrayHelper::getValue(static::getNames(), $methodId);
    }

    /**
     * Get payment method options
     * @return array
     */
    public function getOptions(): array
    {
        return !empty($this->options) ? json_decode($this->options, true) : [];
    }

    /**
     * @return string
     */
    public function getMethodIcon(): string
    {
        $method = PaymentMethods::findOne([$this->method_id]);

        return $method->icon;
    }

    /**
     * Return is passed $currencyCode is supported by this payment gateway
     * @param $currencyCode
     * @return bool
     */
    public function isCurrencySupported($currencyCode)
    {
        // TODO гет метод hasOne из куренси по ИД
        return true;
    }

}
