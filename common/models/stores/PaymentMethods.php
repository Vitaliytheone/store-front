<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use common\models\stores\queries\PaymentMethodsQuery;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property integer $id
 * @property string $method_name
 * @property string $name
 * @property string $class_name
 * @property string $url
 * @property string $settings_form
 * @property string $icon
 * @property string $addfunds_form
 * @property string $settings_form_description
 * @property integer $manual_callback_url
 */
class PaymentMethods extends ActiveRecord
{
    /* Payment methods names */
    public const METHOD_PAYPAL = 'paypal';
    public const METHOD_2CHECKOUT = '2checkout';
    public const METHOD_COINPAYMENTS = 'coinpayments';
    public const METHOD_PAGSEGURO = 'pagseguro';
    public const METHOD_WEBMONEY = 'webmoney';
    public const METHOD_YANDEX_MONEY = 'yandexmoney';
    public const METHOD_YANDEX_CARDS = 'yandexcards';
    public const METHOD_FREE_KASSA = 'freekassa';
    public const METHOD_PAYTR = 'paytr';
    public const METHOD_PAYWANT = 'paywant';
    public const METHOD_BILLPLZ = 'billplz';
    public const METHOD_AUTHORIZE = 'authorize';
    public const METHOD_STRIPE = 'stripe';
    public const METHOD_MERCADOPAGO = 'mercadopago';

    public static $methodsNames = [];

    /** @var array all method_name */
    public static $allMethodsNames = [];

    public static $methods;

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
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['manual_callback_url'], 'integer', 'max' => 1],
            [['settings_form', 'addfunds_form', 'settings_form_description'], 'string'],
            [['method_name', 'name', 'class_name', 'url', 'icon'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Name'),
            'class_name' => Yii::t('app', 'Class Name'),
            'url' => Yii::t('app', 'URL'),
            'settings_form' => Yii::t('app', 'Settings Form'),
            'icon' => Yii::t('app', 'Icon'),
            'addfunds_form' => Yii::t('app', 'Addfunds Form'),
            'settings_form_description' => Yii::t('app', 'Setting Form Description'),
            'manual_callback_url' => Yii::t('app', 'Manual Callback Url'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
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
     * Get currency of current method
     *
     * @return ActiveQuery
     */
    public function getPaymentMethodCurrency(): ActiveQuery
    {
        return $this->hasMany(PaymentMethodsCurrency::class, ['method_id' => 'id']);
    }

    /**
     * Get available payment method Names
     * @return array
     */
    public static function getNames()
    {
        if (empty(static::$methodsNames) || !is_array(static::$methodsNames)) {
            static::$methodsNames = static::find()
                ->select(['name'])
                ->indexBy('method_name')
                ->asArray()
                ->column();
        }

        return static::$methodsNames;
    }

    /**
     * Return payment method title by method
     * @param string $method
     * @return string
     */
    public static function getMethodName(string $method): string
    {
        return ArrayHelper::getValue(static::getNames(), $method, $method);
    }

    /**
     * Get all payment methods only from `method_name` column
     * @return static[]
     */
    public static function getAllMethods(): array
    {
        if (empty(static::$allMethodsNames) || !is_array(static::$allMethodsNames)) {
            static::$allMethodsNames = static::find()
                ->select(['method_name'])
                ->indexBy('id')
                ->asArray()
                ->column();
        }

        return static::$allMethodsNames;
    }

    /**
     * Return value from `method_name` column
     * @param int $method
     * @return string
     */
    public static function getOneMethod(int $method): string
    {
        Yii::debug($method);
        return ArrayHelper::getValue(static::getAllMethods(), $method);
    }

    /**
     * Get all payment methods with options
     * @return static[]
     */
    public static function getMethods():array
    {
        if (empty(static::$methods)) {
            static::$methods = static::find()->all();

        }

        return (array)static::$methods;
    }

    /**
     * Set settings form
     * @param $options
     */
    public function setSettingsForm($options)
    {
        $this->settings_form = json_encode($options);
    }

    /**
     * Get settings form
     * @return array
     */
    public function getSettingsForm(): ?array
    {
        return !empty($this->settings_form) ? json_decode($this->settings_form, true) : [];
    }

    /**
     * Set settings form description
     * @param $description
     */
    public function setSettingsFormDescription($description)
    {
        $this->settings_form_description = json_encode($description);
    }

    /**
     * Get settings form description
     * @return array
     */
    public function getSettingsFormDescription(): array
    {
        return !empty($this->settings_form_description) ? json_decode($this->settings_form_description, true) : [];
    }
}
