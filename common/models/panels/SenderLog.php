<?php

namespace common\models\panels;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sender_log".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $provider_id
 * @property int $send_method
 * @property int $status 1 - Success; 2 - Error; 3 - Curl error
 * @property string $result
 * @property int $created_at
 */
class SenderLog extends ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CURL_ERROR = 3;

    const SEND_METHOD_SIMPLE = 1;
    const SEND_METHOD_PERFECTPANEL = 0;
    const SEND_METHOD_MULTI = 2;
    const SEND_METHOD_MASS = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sender_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'provider_id', 'send_method', 'created_at'], 'integer'],
            [['result'], 'string'],
            [['status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'provider_id' => Yii::t('app', 'Provider ID'),
            'send_method' => Yii::t('app', 'Send Method'),
            'status' => Yii::t('app', 'Status'),
            'result' => Yii::t('app', 'Result'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Get send_method values
     * @return array
     */
    public static function getSendMethods()
    {
        return [
            static::SEND_METHOD_PERFECTPANEL => Yii::t('app/superadmin', 'sender.send_method.perfectpanel'),
            static::SEND_METHOD_SIMPLE => Yii::t('app/superadmin', 'sender.send_method.simple'),
            static::SEND_METHOD_MULTI => Yii::t('app/superadmin', 'sender.send_method.multi'),
            static::SEND_METHOD_MASS => Yii::t('app/superadmin', 'sender.send_method.mass'),
        ];
    }

    /**
     * Get send_method string name
     * @param $sendMethod
     * @return mixed
     */
    public static function getSendMethodName($sendMethod)
    {
        return ArrayHelper::getValue(static::getSendMethods(), $sendMethod, '');
    }
}
