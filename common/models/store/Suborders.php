<?php

namespace common\models\store;

use Yii;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "{{%suborders}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $checkout_id
 * @property string $link
 * @property string $amount
 * @property integer $package_id
 * @property integer $quantity
 * @property integer $status
 * @property integer $updated_at
 * @property integer $mode
 * @property integer $provider_id
 * @property string $provider_service
 * @property string $provider_order_id
 * @property string $provider_charge
 * @property string $provider_response
 *
 * @property Checkouts $checkout
 * @property Orders $order
 * @property Packages $package
 */
class Suborders extends \yii\db\ActiveRecord
{
    /* Suborder status constants */
    const STATUS_AWAITING       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_IN_PROGRESS    = 3;
    const STATUS_COMPLETED      = 4;
    const STATUS_CANCELED       = 5;
    const STATUS_FAILED         = 6;
    const STATUS_ERROR          = 7;

    /* Suborder mode constants */
    const MODE_MANUAL           = 0;
    const MODE_AUTO             = 1;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'updated_at',
                    self::EVENT_BEFORE_UPDATE => 'updated_at',

                ],
                'value' => function ($event) {
                    return time();
                },
            ],
        ];
    }


    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%suborders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'checkout_id', 'package_id', 'quantity', 'status', 'updated_at', 'mode', 'provider_id'], 'integer'],
            [['amount', 'provider_charge'], 'number'],
            [['provider_response'], 'string'],
            [['link'], 'string', 'max' => 1000],
            [['provider_service', 'provider_order_id'], 'string', 'max' => 300],
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::className(), 'targetAttribute' => ['checkout_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['package_id'], 'exist', 'skipOnError' => true, 'targetClass' => Packages::className(), 'targetAttribute' => ['package_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'checkout_id' => Yii::t('app', 'Checkout ID'),
            'link' => Yii::t('app', 'Link'),
            'amount' => Yii::t('app', 'Amount'),
            'package_id' => Yii::t('app', 'Package ID'),
            'quantity' => Yii::t('app', 'Quantity'),
            'status' => Yii::t('app', 'Status'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'mode' => Yii::t('app', '0 - manual, 1 - auto'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'provider_service' => Yii::t('app', 'Provider Service'),
            'provider_order_id' => Yii::t('app', 'Provider Order ID'),
            'provider_charge' => Yii::t('app', 'Provider Charge'),
            'provider_response' => Yii::t('app', 'Provider Response'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckout()
    {
        return $this->hasOne(Checkouts::className(), ['id' => 'checkout_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackage()
    {
        return $this->hasOne(Packages::className(), ['id' => 'package_id']);
    }

    /**
     * Return Suborder Details data
     * Defines is returned provider response
     * plain Json string or print_r formatted string
     * @param bool $responseFormatPrintR
     * @return array|null
     */
    public function getDetails(bool $responseFormatPrintR = true)
    {
        $provider = (new \yii\db\Query())
            ->select(['site'])
            ->from("providers")
            ->where(['id' => $this->provider_id])
            ->one();
        if (!$provider) {
            return null;
        }
        $providerResponse = $this->provider_response;
        if ($responseFormatPrintR) {
            $providerResponse = print_r(json_decode($providerResponse),1);
        }
        $formatter = Yii::$app->formatter;
        $orderDetails = [
            'provider' => $provider['site'],
            'provider_order_id' => $this->provider_order_id,
            'provider_response' => $providerResponse,
            'updated_at' => $formatter->asDatetime($this->updated_at,'yyyy-MM-dd HH:mm:ss'),
        ];
        return $orderDetails;
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\SubordersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\SubordersQuery(get_called_class());
    }
}
