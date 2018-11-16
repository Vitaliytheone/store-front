<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\queries\PaymentMethodsQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property int $id
 * @property string $method_name
 * @property string $class_name
 * @property string $url
 * @property string $addfunds_form
 * @property string $settings_form
 * @property string $settings_form_description
 * @property int $manual_callback_url
 * @property int $take_fee_from_user
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PanelPaymentMethods[] $panelPaymentMethods
 */
class PaymentMethods extends ActiveRecord
{
    const HIDDEN_ENABLED = 1;
    const HIDDEN_DISABLED = 0;

    const FIELD_TYPE_INPUT = 'input';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_MULTI_INPUT = 'multi_input';
    const FIELD_TYPE_SELECT = 'select';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_COURSE = 'course';

    const VISIBILITY_ENABLED = 1;
    const VISIBILITY_DISABLED = 0;

    const METHOD_OTHER = 0;
    const METHOD_AUTO = 101;
    const METHOD_BONUS = 102;

    const METHOD_PAYPAL = 1;
    const METHOD_PERFECT_MONEY_USD = 2;
    const METHOD_WEBMONEY_USD = 3;
    const METHOD_PAYZA = 4;
    const METHOD_TWO_CHECKOUT = 5;
    const METHOD_SKRILL = 6;
    const METHOD_YANDEX_MONEY = 10;
    const METHOD_INTERKASSA = 12;
    const METHOD_LIQPAY = 13;
    const METHOD_BITCOIN = 14;
    const METHOD_ZARINPAL = 7;
    const METHOD_YANDEX_KASSA = 15;
    const METHOD_PAY_UMONEY = 16;
    const METHOD_PAYWANT = 17;
    const METHOD_FREEKASSA = 18;
    const METHOD_PAYEER = 19;
    const METHOD_BILLPLZ = 20;
    const METHOD_COINPAYMENTS = 21;
    const METHOD_MERCADOPADO = 22;
    const METHOD_DIGISELLER = 23;
    const METHOD_TAP = 24;
    const METHOD_INSTAMOJO = 25;
    const METHOD_PAGSEGURO = 26;
    const METHOD_UNITPAY = 28;
    const METHOD_PAYTR = 29;
    const METHOD_DOKU = 30;
    const METHOD_HESABE = 31;
    const METHOD_STRIPE = 32;
    const METHOD_PAYWITHPAYTM = 33;
    const METHOD_KLIKBCA = 35;
    const METHOD_YANDEX_CARDS = 36;
    const METHOD_PAYTM_IMAP = 37;
    const METHOD_AUTHORIZE = 38;
    const METHOD_COMMERCE_COINBASE = 39;
    const METHOD_BUYPAYER = 40;
    const METHOD_PRZELEWY24 = 41;
    const METHOD_QIWI = 42;
    const METHOD_PAYTR_HAVALE = 43;
    const METHOD_MASTERCARD = 44;
    const METHOD_KBANK = 45;
    const METHOD_PAYPAL_INVOICE = 46;
    const METHOD_SENANGPAY = 47;
    const METHOD_PAYAMAR = 48;
    const METHOD_MIDTRANS = 49;
    const METHOD_PAYGURU = 50;
    const METHOD_NGANLUONG = 51;
    const METHOD_MOLLIE = 52;
    const METHOD_PAYPAL_STANDARD = 53;
    const METHOD_WEBMONEY_RUB = 55;
    const METHOD_WEBMONEY_EUR = 56;
    const METHOD_PERFECT_MONEY_EUR = 57;


    use UnixTimeFormatTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment_methods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['method_name', 'class_name', 'url'], 'required'],
            [['addfunds_form', 'settings_form', 'settings_form_description'], 'string'],
            [['manual_callback_url', 'created_at', 'updated_at', 'take_fee_from_user'], 'integer'],
            [['method_name', 'class_name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'method_name' => Yii::t('app', 'Method Name'),
            'class_name' => Yii::t('app', 'Class Name'),
            'url' => Yii::t('app', 'Url'),
            'addfunds_form' => Yii::t('app', 'Addfunds Form'),
            'settings_form' => Yii::t('app', 'Settings Form'),
            'settings_form_description' => Yii::t('app', 'Settings Form Description'),
            'manual_callback_url' => Yii::t('app', 'Manual Callback Url'),
            'take_fee_from_user' => Yii::t('app', 'Take fee from user'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelPaymentMethods()
    {
        return $this->hasMany(PanelPaymentMethods::class, ['method_id' => 'id']);
    }

    /**
     * {@inheritdoc}
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

    /**
     * @return array
     */
    public static function getAdditionalMethods()
    {
        return [
            static::METHOD_AUTO,
            static::METHOD_BONUS
        ];
    }
}