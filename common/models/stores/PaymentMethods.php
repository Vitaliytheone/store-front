<?php

namespace common\models\stores;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use common\models\stores\queries\PaymentMethodsQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property integer $id
 * @property string $method_name
 * @property string $name
 * @property string $class_name
 * @property string $url
 * @property string $settings_form
 * @property string $settings_form_description
 * @property string $icon
 * @property string $addfunds_form
 * @property integer $manual_callback_url
 * @property integer created_at
 * @property integer updated_at
 *
 * @property PaymentMethodsCurrency[] $paymentMethodCurrency
 */
class PaymentMethods extends ActiveRecord
{
    /* Payment methods names */
    public const METHOD_PAYPAL = 1;
    public const METHOD_2CHECKOUT = 2;
    public const METHOD_COINPAYMENTS = 3;
    public const METHOD_PAGSEGURO = 4;
    public const METHOD_WEBMONEY = 5;
    public const METHOD_YANDEX_MONEY = 6;
    public const METHOD_FREE_KASSA = 7;
    public const METHOD_PAYTR = 8;
    public const METHOD_PAYWANT = 9;
    public const METHOD_BILLPLZ = 10;
    public const METHOD_AUTHORIZE = 11;
    public const METHOD_YANDEX_CARDS = 12;
    public const METHOD_STRIPE = 13;
    public const METHOD_MERCADOPAGO = 14;
    public const METHOD_PAYPAL_STANDARD = 15;
    public const METHOD_MOLLIE = 16;
    public const METHOD_STRIPE_3D_SECURE = 17;

    public const FIELD_TYPE_INPUT = 'input';
    public const FIELD_TYPE_CHECKBOX = 'checkbox';
    public const FIELD_TYPE_MULTI_INPUT = 'multi_input';
    public const FIELD_TYPE_SELECT = 'select';
    public const FIELD_TYPE_TEXTAREA = 'textarea';

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
     * Get all currency of current payment method
     *
     * @return ActiveQuery
     */
    public function getPaymentMethodCurrency(): ActiveQuery
    {
        return $this->hasMany(PaymentMethodsCurrency::class, ['method_id' => 'id']);
    }

    /**
     * Get name attribute value
     * @param int $id
     * @return string
     */
    public static function getName(int $id): string
    {
        $method = static::findOne($id);

        if (!$method) {
            return false;
        }

        return $method->name ?? $method->method_name;
    }

    /**
     * Get list of methods names ['id' => 'name']
     * @return array
     */
    public static function getNamesList(): array
    {
        if (empty(static::$allMethodsNames) || !is_array(static::$allMethodsNames)) {
            $methodsNames = static::find()
                ->select(['method_name', 'id'])
                ->indexBy('id')
                ->asArray()
                ->all();
            static::$allMethodsNames = ArrayHelper::map($methodsNames, 'id', 'method_name');
        }
        return static::$allMethodsNames;
    }

    /**
     * Return value from `class_name` column
     * @param int $id
     * @return string|null
     */
    public static function getClassName(int $id): ?string
    {
        $method = static::find()
            ->select(['class_name'])
            ->where(['id' => $id])
            ->one();

        return $method->class_name ?? null;
    }

    /**
     * Get all payment methods with options
     * @return static[]
     */
    public static function getMethods(): array
    {
        if (empty(static::$methods)) {
            static::$methods = static::find()->indexBy('id')->all();

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
    public function getSettingsForm(): array
    {
        return !empty($this->settings_form) ? json_decode($this->settings_form, true) : [];
    }

    /**
     * Set settings form description
     * @param $description
     */
    public function setSettingsFormDescription($description)
    {
        $this->settings_form_description = $description;
    }

    /**
     * Get settings form description
     * @return string
     */
    public function getSettingsFormDescription(): string
    {
        return !empty($this->settings_form_description) ? $this->settings_form_description : '';
    }

}
