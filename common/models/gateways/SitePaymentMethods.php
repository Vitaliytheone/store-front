<?php

namespace common\models\gateways;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateways\queries\SitePaymentMethodsQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%site_payment_methods}}".
 *
 * @property int $id
 * @property int $site_id
 * @property int $method_id
 * @property string $options
 * @property int $visibility
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Sites $site
 * @property PaymentMethods $method
 */
class SitePaymentMethods extends ActiveRecord
{
    public const VISIBILITY_ENABLED = 1;
    public const VISIBILITY_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site_payment_methods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'method_id'], 'required'],
            [['id', 'site_id', 'method_id', 'created_at', 'updated_at', 'visibility'], 'integer'],
            [['options'], 'string'],
            [['site_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sites::class, 'targetAttribute' => ['site_id' => 'id']],
            [['method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethods::class, 'targetAttribute' => ['method_id' => 'id']],
            [['options'], 'default', 'value' => '[]'],
            [['visibility'], 'default', 'value' => static::VISIBILITY_DISABLED],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'site_id' => Yii::t('app', 'Site ID'),
            'method_id' => Yii::t('app', 'Method ID'),
            'options' => Yii::t('app', 'Options'),
            'visibility' => Yii::t('app', 'Visibility'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSite()
    {
        return $this->hasOne(Sites::class, ['id' => 'site_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMethod()
    {
        return $this->hasOne(PaymentMethods::class, ['id' => 'method_id']);
    }

    /**
     * @inheritdoc
     * @return SitePaymentMethodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SitePaymentMethodsQuery(get_called_class());
    }

    /**
     * Get payment method details
     * @return array|mixed
     */
    public function getOptionsDetails()
    {
        return !empty($this->options) ? json_decode($this->options, true) : [];
    }

    /**
     * @param array $options
     */
    public function setOptionsDetails($options)
    {
        $paymentMethodOptions = PaymentMethods::findOne($this->method_id)->getFormSettings();

        $preparedOptions = [];

        foreach ($paymentMethodOptions as $paymentMethodOption) {
            $preparedOptions[$paymentMethodOption['name']] = ArrayHelper::getValue($options, $paymentMethodOption['name']);
        }

        $this->options = json_encode((array)$preparedOptions);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}