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
 * @property string $currencies
 * @property string $name
 */
class PaymentGateways extends ActiveRecord
{
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
            [['method','name'], 'string', 'max' => 255],
            [['currencies'], 'string', 'max' => 3000],
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
            'currencies' => Yii::t('app', 'Currencies'),
            'name' => Yii::t('app', 'Name'),
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
     * Return is passed $currencyCode is supported by this payment gateway
     * @param $currencyCode
     * @return bool
     */
    public function isCurrencySupported($currencyCode)
    {
        return in_array($currencyCode, $this->getCurrencies());
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
