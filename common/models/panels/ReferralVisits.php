<?php

namespace common\models\panels;

use app\components\behaviors\IpBehavior;
use app\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\ReferralVisitsQuery;

/**
 * This is the model class for table "{{%referral_visits}}".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $ip
 * @property string $user_agent
 * @property string $http_referer
 * @property string $request_data
 * @property integer $created_at
 */
class ReferralVisits extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%referral_visits}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'ip', 'user_agent', 'http_referer', 'request_data', 'created_at'], 'required'],
            [['customer_id', 'created_at'], 'integer'],
            [['request_data'], 'string'],
            [['ip', 'user_agent', 'http_referer'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'ip' => Yii::t('app', 'Ip'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'http_referer' => Yii::t('app', 'Http Referer'),
            'request_data' => Yii::t('app', 'Request Data'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return ReferralVisitsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReferralVisitsQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
            'ip' => [
                'class' => IpBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
        ];
    }
}
