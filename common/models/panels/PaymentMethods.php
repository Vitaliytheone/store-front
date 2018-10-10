<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use common\models\panels\queries\PaymentMethodsQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property int $id
 * @property string $currency
 * @property string $code
 * @property string $method_name
 * @property string $class_name
 * @property string $url
 * @property string $addfunds_form
 * @property string $settings_form
 * @property string $settings_form_description
 * @property string $multi_currency
 * @property int $position
 * @property int $hidden
 * @property int $auto_exchange_rate
 * @property int $manual_callback_url
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PanelPaymentMethods[] $panelPaymentMethods
 */
class PaymentMethods extends ActiveRecord
{
    public const HIDDEN_ENABLED = 1;
    public const HIDDEN_DISABLED = 0;

    public const FIELD_TYPE_INPUT = 'input';
    public const FIELD_TYPE_MULTI_INPUT = 'multi_input';
    public const FIELD_TYPE_SELECT = 'select';
    public const FIELD_TYPE_TEXTAREA = 'textarea';

    public const VISIBILITY_ENABLED = 1;
    public const VISIBILITY_DISABLED = 0;

    public const METHOD_PAYPAL = 1;
    public const METHOD_PERFECT_MONEY = 2;
    public const METHOD_WEBMONEY = 3;
    public const METHOD_PAYZA = 4;
    public const METHOD_TWO_CHECKOUT = 5;
    public const METHOD_SKRILL = 6;
    public const METHOD_YANDEX_MONEY = 10;
    public const METHOD_INTERKASSA = 12;
    public const METHOD_LIQPAY = 13;
    public const METHOD_BITCOIN = 14;
    public const METHOD_ZARINPAL = 7;
    public const METHOD_YANDEX_KASSA = 15;
    public const METHOD_PAY_UMONEY = 16;
    public const METHOD_PAYWANT = 17;
    public const METHOD_FREEKASSA = 18;
    public const METHOD_PAYEER = 19;
    public const METHOD_BILLPLZ = 20;
    public const METHOD_COINPAYMENTS = 21;
    public const METHOD_MERCADOPADO = 22;
    public const METHOD_DIGISELLER = 23;
    public const METHOD_TAP = 24;
    public const METHOD_INSTAMOJO = 25;
    public const METHOD_PAGSEGURO = 26;
    public const METHOD_PAYTM = 27;
    public const METHOD_UNITPAY = 28;
    public const METHOD_PAYTR = 29;
    public const METHOD_DOKU = 30;
    public const METHOD_HESABE = 31;
    public const METHOD_STRIPE = 32;
    public const METHOD_PAYWITHPAYTM = 33;
    public const METHOD_COINBASE = 34;
    public const METHOD_KLIKBCA = 35;
    public const METHOD_YANDEX_CARDS = 36;
    public const METHOD_PAYTM_IMAP = 37;
    public const METHOD_AUTHORIZE = 38;
    public const METHOD_COMMERCE_COINBASE = 39;
    public const METHOD_BUYPAYER = 40;
    public const METHOD_PRZELEWY24 = 41;
    public const METHOD_QIWI = 42;
    public const METHOD_PAYTR_HAVALE = 43;
    public const METHOD_MASTERCARD = 44;
    public const METHOD_KBANK = 45;
    public const METHOD_PAYPAL_INVOICE = 46;
    public const METHOD_SENANGPAY = 47;
    public const METHOD_PAYAMAR = 48;
    public const METHOD_MIDTRANS = 49;
    public const METHOD_PAYGURU = 50;
    public const METHOD_NGANLUONG = 51;
    public const METHOD_MOLLIE = 52;
    public const METHOD_PAYPAL_STANDARD = 53;

    use UnixTimeFormatTrait;
    
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
            [['currency', 'method_name', 'class_name', 'url'], 'required'],
            [['addfunds_form', 'settings_form', 'settings_form_description', 'multi_currency', 'currency'], 'string'],
            [['position', 'hidden', 'auto_exchange_rate', 'manual_callback_url', 'created_at', 'updated_at'], 'integer'],
            [['method_name', 'class_name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'currency' => Yii::t('app', 'Currency'),
            'code' => Yii::t('app', 'Code'),
            'method_name' => Yii::t('app', 'Method Name'),
            'class_name' => Yii::t('app', 'Class Name'),
            'url' => Yii::t('app', 'Url'),
            'addfunds_form' => Yii::t('app', 'Addfunds Form'),
            'settings_form' => Yii::t('app', 'Settings Form'),
            'settings_form_description' => Yii::t('app', 'Settings Form Description'),
            'multi_currency' => Yii::t('app', 'Multi Currency'),
            'position' => Yii::t('app', 'Position'),
            'hidden' => Yii::t('app', 'Hidden'),
            'auto_exchange_rate' => Yii::t('app', 'Auto Exchange Rate'),
            'manual_callback_url' => Yii::t('app', 'Manual Callback Url'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelPaymentMethods()
    {
        return $this->hasMany(PanelPaymentMethods::className(), ['method_id' => 'id']);
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
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return array|mixed
     */
    public function getSettingsForm()
    {
        return !empty($this->settings_form) ? json_decode($this->settings_form, true) : [];
    }

    /**
     * @param $options
     */
    public function setSettingsForm($options)
    {
        $this->settings_form = json_encode($options);
    }

    /**
     * @return array|mixed
     */
    public function getAddfundsForm()
    {
        return !empty($this->addfunds_form) ? json_decode($this->addfunds_form, true) : [];
    }

    /**
     * @param $options
     */
    public function setAddfundsForm($options)
    {
        $this->addfunds_form = json_encode($options);
    }
}