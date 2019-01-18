<?php

namespace common\models\gateways;

use Yii;
use yii\db\ActiveRecord;
use common\models\gateways\queries\PaymentMethodsQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%payment_methods}}".
 *
 * @property int $id
 * @property string $method_name
 * @property string $class_name
 * @property string $url
 *
 * @property SitePaymentMethods[] $sitePaymentMethods
 */
class PaymentMethods extends ActiveRecord
{
    public const METHOD_PAYPAL = 1;
    public const METHOD_STRIPE = 2;

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
            [['method_name', 'class_name', 'url'], 'required'],
            [['method_name', 'class_name', 'url'], 'string', 'max' => 300],
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
            'class_name' => Yii::t('app', 'Class Name'),
            'url' => Yii::t('app', 'Url'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSitePaymentMethods()
    {
        return $this->hasMany(SitePaymentMethods::class, ['method_id' => 'id']);
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
     * Return payments methods list item data
     * @return mixed
     */
    public static function getViewData()
    {
        return [
            static::METHOD_PAYPAL => [
                'icon' => '/img/pg/paypal.png',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_username', 'placeholder' => '', 'name' => 'username', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_paypal_username')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_password', 'placeholder' => '', 'name' => 'password', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_paypal_password')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_signature', 'placeholder' => '', 'name' => 'signature', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_paypal_signature')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'paypal_description', 'placeholder' => '', 'name' => 'description', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_paypal_description')],
                    ['tag' => 'input', 'type' => 'checkbox', 'name' => 'test_mode', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_paypal_test_mode')],
                ]
            ],
            static::METHOD_STRIPE => [
                'icon' => '/img/pg/stripe_logo.png',
                'icon_style' => 'margin: 10px;',
                'form_fields' => [
                    ['tag' => 'input', 'type' => 'text', 'id' => 'stripe_secret_key', 'placeholder' => '', 'name' => 'secret_key', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_stripe_secret_key')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'stripe_public_key', 'placeholder' => '', 'name' => 'public_key', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_stripe_public_key')],
                    ['tag' => 'input', 'type' => 'text', 'id' => 'stripe_webhook_secret', 'placeholder' => '', 'name' => 'webhook_secret', 'value' => '', 'label' => Yii::t('admin', 'settings.payments_stripe_webhook_secret')]
                ]
            ],
        ];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return (string)ArrayHelper::getValue(static::getViewData(), [$this->id, 'icon'], '');
    }

    /**
     * @return string
     */
    public function getIconStyle()
    {
        return (string)ArrayHelper::getValue(static::getViewData(), [$this->id, 'icon_style'], '');
    }

    /**
     * @return array
     */
    public function getFormSettings()
    {
        return (array)ArrayHelper::getValue(static::getViewData(), [$this->id, 'form_fields'], []);
    }
}