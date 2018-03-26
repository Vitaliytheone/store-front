<?php

namespace common\models\panel;

use my\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "payments_log".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $response
 * @property string $logs
 * @property integer $date
 * @property integer $ip
 */
class PaymentsLog extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payments_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'logs', 'date', 'ip'], 'required'],
            [['pid', 'date'], 'integer'],
            [['response', 'logs'], 'string', 'max' => 100000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'response' => 'Response',
            'logs' => 'Logs',
            'date' => 'Date',
            'ip' => 'Ip',
        ];
    }

    /**
     * Set response
     * @param array $response
     */
    public function setResponse($response)
    {
        $this->response = Json::encode($response);
    }

    /**
     * Get response
     * @return array $response
     */
    public function getResponse()
    {
        return Json::decode($this->response);
    }
}
