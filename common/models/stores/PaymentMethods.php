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
    const METHOD_PAYPAL = 'paypal';
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
            [['id'], 'integer'],
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
     * Get available payment method names
     * @return array
     */
    public static function getNames()
    {
        if (empty(static::$methodsNames) || !is_array(static::$methodsNames)) {
            static::$methodsNames = PaymentGateways::find()
                ->select(['name'])
                ->indexBy('method_name')
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
     * Get method name
     * @param string $method
     * @return string
     */
    public static function getMethodName(string $method):string
    {
        return (string)ArrayHelper::getValue(ArrayHelper::index(static::getMethods(), 'method'), [$method, 'name'], $method);
    }

//    /**
//     * Get payment method details
//     * @return array|mixed
//     */
//    public function getDetails()
//    {
//        return !empty($this->details) ? json_decode($this->details, true) : [];
//    }

    /**
     * @return mixed
     */
    public function getCurrencies()
    {
        $currenciesList = json_decode($this->currencies, true);

        if (!is_array($currenciesList)) {
            $currenciesList = [];
        }

        return $currenciesList;
    }

    /**
     * @return mixed
     */
    public function getOptions():array
    {
        return (array)json_decode($this->options, true);
    }

    /**
     * Return is passed $currencyCode is supported by this payment gateway
     * @param $currencyCode
     * @return bool
     */
    public function isCurrencySupported($currencyCode)
    {
        return in_array($currencyCode, $this->getCurrencies());
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
     * Return payments methods list which is supported this $currencyCode
     * @param $currencyCode string
     * @param $onlyMethods boolean Return only method code or full method data array
     * @return array
     */
    public static function getSupportedMethods($currencyCode, $onlyMethods  = true)
    {
        $methods = static::find()->asArray()->all();

        foreach ($methods as $key => &$method) {
            $supportedCurrencies = json_decode(ArrayHelper::getValue($method, 'currencies', []), true);

            if (!in_array($currencyCode, $supportedCurrencies)) {
                unset($methods[$key]);
                continue;
            }

            if ($onlyMethods) {
                $method = $method['method'];
            }
        }

        return $methods;
    }
}
