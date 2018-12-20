<?php

namespace common\models\stores;

use Yii;
use \yii\db\ActiveRecord;
use \common\models\stores\queries\PaymentGatewaysQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "payment_gateways".
 *
 * @property integer $id
 * @property string $method
 * @property string $name
 * @property string $class_name
 * @property string $url
 * @property integer $position
 * @property string $options
 * @property string $currencies
 * @property integer $visibility 0 - hide, 1 - visible
 */
class PaymentGateways extends ActiveRecord
{
    public const GATEWAY_PUBLIC = 1;
    public const GATEWAY_HIDE = 0;

    public static $methods;

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
            [['method', 'name', 'class_name', 'url'], 'string', 'max' => 255],
            [['position', 'visibility'], 'integer'],
            [['currencies', 'options'], 'string', 'max' => 3000],
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
            'name' => Yii::t('app', 'Name'),
            'class_name' => Yii::t('app', 'Class name'),
            'url' => Yii::t('app', 'Url'),
            'position' => Yii::t('app', 'Position'),
            'options' => Yii::t('app', 'Options'),
            'currencies' => Yii::t('app', 'Currencies'),
            'visibility' => Yii::t('app', 'Visibility'),
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

    /**
     * Get method name
     * @param string $method
     * @return string
     */
    public static function getMethodName(string $method):string
    {
        return (string)ArrayHelper::getValue(ArrayHelper::index(static::getMethods(), 'method'), [$method, 'name'], $method);
    }
}
