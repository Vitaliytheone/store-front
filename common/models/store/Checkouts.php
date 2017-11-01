<?php

namespace common\models\store;

use Yii;

/**
 * This is the model class for table "{{%checkouts}}".
 *
 * @property integer $id
 * @property string $customer
 * @property string $price
 * @property integer $status
 * @property string $method_status
 * @property integer $method_id
 * @property string $ip
 * @property string $details
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $currency
 *
 * @property Orders[] $orders
 * @property Payments[] $payments
 * @property PaymentsLog[] $paymentsLogs
 * @property Suborders[] $suborders
 */
class Checkouts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%checkouts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'status', 'method_id', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['details'], 'string'],
            [['customer', 'method_status', 'ip'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer' => Yii::t('app', 'Customer'),
            'price' => Yii::t('app', 'Price'),
            'status' => Yii::t('app', '0 - pending
1 - paid
'),
            'method_status' => Yii::t('app', 'Method Status'),
            'method_id' => Yii::t('app', 'Method ID'),
            'ip' => Yii::t('app', 'Ip'),
            'details' => Yii::t('app', 'json
link
quantity
package_id'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'currency' => Yii::t('app', 'Currency'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['checkout_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::className(), ['checkout_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentsLogs()
    {
        return $this->hasMany(PaymentsLog::className(), ['checkout_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuborders()
    {
        return $this->hasMany(Suborders::className(), ['checkout_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\CheckoutsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\CheckoutsQuery(get_called_class());
    }
}
