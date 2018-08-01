<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\panels\queries\SenderLogQuery;

/**
 * This is the model class for table "{{%sender_log}}".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $provider_id
 * @property int $send_method
 * @property integer $status
 * @property string $result
 * @property int $created_at
 */
class SenderLog extends ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CURL_ERROR = 3;

    use UnixTimeFormatTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sender_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panel_id', 'provider_id', 'send_method', 'created_at', 'status'], 'integer'],
            [['result'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     * @return SenderLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SenderLogQuery(get_called_class());
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
        ];
    }

    /**
     * @param $result
     */
    public function setResult($result)
    {
        $this->result = json_encode($result);
    }

    /**
     * @return array|mixed
     */
    public function getResult()
    {
        return $this->result ? json_decode($this->result) : [];
    }

    /**
     * Log method
     * @param integer $panelId
     * @param integer $providerId
     * @param mixed $result
     * @param integer $senderMethod
     * @return mixed
     */
    public static function log($panelId, $providerId, $result = null, $senderMethod = 0)
    {
        $status = static::STATUS_SUCCESS;

        if (empty($result['success'])) {
            $status = static::STATUS_ERROR;
        }

        return (new static([
            'panel_id' => (integer)$panelId,
            'provider_id' => (integer)$providerId,
            'send_method' => $senderMethod,
            'status' => $status,
            'result' => json_encode($result)
        ]))->save();
    }
}