<?php

namespace common\models\store;

use Yii;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property integer $id
 * @property integer $checkout_id
 * @property string $customer
 * @property integer $created_at
 *
 * @property Checkouts $checkout
 * @property Suborders[] $suborders
 */
class Orders extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'checkout_id', 'created_at'], 'integer'],
            [['customer'], 'string', 'max' => 255],
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
            'customer' => Yii::t('app', 'Customer'),
            'created_at' => Yii::t('app', 'Created At'),
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
    public function getSuborders()
    {
        return $this->hasMany(Suborders::className(), ['order_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\queries\OrdersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\OrdersQuery(get_called_class());
    }
}
