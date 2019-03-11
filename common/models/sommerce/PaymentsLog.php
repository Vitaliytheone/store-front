<?php

namespace common\models\sommerce;

use common\components\behaviors\IpBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerce\queries\PaymentsLogQuery;

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
class PaymentsLog extends ActiveRecord
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
            [['checkout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Checkouts::class, 'targetAttribute' => ['checkout_id' => 'id']],
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
        return $this->hasOne(Checkouts::class, ['id' => 'checkout_id']);
    }

    /**
     * @inheritdoc
     * @return PaymentsLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentsLogQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
            'ip' => [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ],
                'defaultValue' => ' '
            ],
        ];
    }

    /**
     * Set result
     * @param array $result
     */
    public function setResult($result)
    {
        $this->result = @json_encode($result);
    }

    /**
     * Get response
     * @return array $response
     */
    public function getResult()
    {
        return @json_decode($this->result, true);
    }

    /**
     * Log payment
     * @param $checkoutId
     * @param $result
     */
    public static function log($checkoutId, $result)
    {
        $log = new static();
        $log->checkout_id = $checkoutId;
        $log->setResult($result);
        $log->save(false);
    }
}
