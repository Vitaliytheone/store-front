<?php

namespace common\models\store;

use Yii;

/**
 * This is the model class for table "{{%payments}}".
 *
 * @property integer $id
 * @property integer $checkout_id
 * @property integer $method_id
 * @property string $customer
 * @property string $amount
 * @property integer $status
 * @property string $fee
 * @property string $transaction_id
 * @property string $name
 * @property string $email
 * @property string $country
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $currency
 *
 * @property Checkouts $checkout
 */
class Payments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'checkout_id', 'method_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'fee'], 'number'],
            [['customer', 'transaction_id', 'name', 'email', 'country'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 10],
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::className(), 'targetAttribute' => ['checkout_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'checkout_id' => Yii::t('app', 'Checkout ID'),
            'method_id' => Yii::t('app', 'Method ID'),
            'customer' => Yii::t('app', 'Customer'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', '1 - Completed
2 - Awating
3 - Failed
4 - Refunded'),
            'fee' => Yii::t('app', 'Fee'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'name' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'country' => Yii::t('app', 'Country'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'currency' => Yii::t('app', 'Currency'),
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
     * @inheritdoc
     * @return \common\models\store\queries\PaymentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\PaymentsQuery(get_called_class());
    }
}
