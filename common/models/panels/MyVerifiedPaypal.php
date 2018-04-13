<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use  \yii\db\ActiveRecord;
use common\models\panels\queries\MyVerifiedPaypalQuery;

/**
 * This is the model class for table "my_verified_paypal".
 *
 * @property integer $id
 * @property integer $payment_id
 * @property string $paypal_payer_id
 * @property string $paypal_payer_email
 * @property integer $verified
 * @property integer $updated_at
 * @property integer $created_at
 */
class MyVerifiedPaypal extends ActiveRecord
{
    const STATUS_VERIFIED = 1;
    const STATUS_NOT_VERIFIED = 0;

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.my_verified_paypal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'updated_at', 'created_at'], 'integer'],
            [['paypal_payer_id'], 'string', 'max' => 100],
            [['paypal_payer_email'], 'string', 'max' => 300],
            [['verified'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'paypal_payer_id' => Yii::t('app', 'Payer ID'),
            'paypal_payer_email' => Yii::t('app', 'Payer email'),
            'verified' => Yii::t('app', 'Verified'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return MyVerifiedPaypalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MyVerifiedPaypalQuery(get_called_class());
    }
}
