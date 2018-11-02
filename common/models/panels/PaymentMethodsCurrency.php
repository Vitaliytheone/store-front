<?php

namespace common\models\panels;

use common\models\panels\queries\PaymentMethodsCurrencyQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%payment_methods_currency}}".
 *
 * @property int $id
 * @property int $method_id
 * @property string $currency
 * @property string $exchange_currency
 * @property int $position
 * @property string $settings_form
 * @property string $settings_form_description
 * @property int $auto_exchange_rate
 * @property int $hidden
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PaymentMethods $method
 */
class PaymentMethodsCurrency extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%payment_methods_currency}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['method_id', 'currency', 'position'], 'required'],
            [['method_id', 'position', 'auto_exchange_rate', 'hidden', 'created_at', 'updated_at'], 'integer'],
            [['exchange_currency', 'settings_form_description', 'settings_form'], 'string'],
            [['currency'], 'string', 'max' => 3],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'method_id' => Yii::t('app', 'Method ID'),
            'currency' => Yii::t('app', 'Currency'),
            'exchange_currency' => Yii::t('app', 'Exchange currency'),
            'position' => Yii::t('app', 'Position'),
            'settings_form' => Yii::t('app', 'Settings Form'),
            'settings_form_description' => Yii::t('app', 'Settings Form Description'),
            'auto_exchange_rate' => Yii::t('app', 'Auto Exchange Rate'),
            'hidden' => Yii::t('app', 'Hidden'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMethod()
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * {@inheritdoc}
     * @return PaymentMethodsCurrencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentMethodsCurrencyQuery(get_called_class());
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
}