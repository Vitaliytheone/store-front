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
