<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
class StorePaymentMethods extends ActiveRecord
{
    public const VISIBILITY_DISABLED = 0;
    public const VISIBILITY_ENABLED = 1;

    public static $paymentsNames = [];

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
            [['store_id'], 'required'],
            [['store_id', 'method_id', 'currency_id', 'position', 'created_at', 'updated_at'], 'integer'],
            [['options'], 'string'],
            [['visibility'], 'integer', 'max' => 1],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stores::class, 'targetAttribute' => ['store_id' => 'id']],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethodsCurrency::class, 'targetAttribute' => ['currency_id' => 'id']],
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
     * Get store of current method
     * @return ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Stores::class, ['id' => 'store_id']);
    }

    /**
     * Get payment method of current store payment method
     * @return ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * Get currency of current method
     * @return ActiveQuery
     */
    public function getStorePaymentMethodCurrency()
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
        if (empty(static::$paymentsNames) || !is_array(static::$paymentsNames)) {
            static::$paymentsNames = PaymentMethods::find()
                ->select(['name'])
                ->indexBy('id')
                ->asArray()
                ->column();
        }

        return static::$paymentsNames;
    }

    /**
     * Get current payment method Name (title)
     * @return string|null
     */
    public function getName(): ?string
    {
        return ArrayHelper::getValue(static::getNames(), $this->method_id);
    }

    /**
     * Return payment method Name (title) by method_id
     * @param int $methodId
     * @return string|null
     */
    public static function getNameById($methodId): ?string
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
     * Get current method icon
     * @return string
     */
    public function getMethodIcon(): string
    {
        $method = PaymentMethods::findOne([$this->method_id]);

        return $method->icon ?? '';
    }

    /**
     * @return int
     */
    public static function getLastPosition(): int
    {
        $last = static::find()->max('position');
        return isset($last) ? $last : 0;
    }
}
