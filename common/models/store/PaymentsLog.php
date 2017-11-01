<?php

namespace common\models\store;

use Yii;

/**
 * This is the model class for table "{{%payments_log}}".
 *
 * @property integer $id
 * @property integer $checkout_id
 * @property string $result
 * @property string $ip
 * @property integer $created_at
 *
 * @property Checkouts $checkout
 */
class PaymentsLog extends \yii\db\ActiveRecord
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
        return '{{%payments_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'checkout_id', 'created_at'], 'integer'],
            [['result'], 'string'],
            [['ip'], 'string', 'max' => 255],
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
            'result' => Yii::t('app', 'Result'),
            'ip' => Yii::t('app', 'Ip'),
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
     * @inheritdoc
     * @return \common\models\store\queries\PaymentsLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\store\queries\PaymentsLogQuery(get_called_class());
    }
}
