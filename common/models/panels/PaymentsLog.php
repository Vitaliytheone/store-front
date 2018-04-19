<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
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
        return DB_PANELS . '.payments_log';
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

    /**
     * Get log
     * @return mixed
     */
    public function getLog()
    {
        return Json::decode($this->logs);
    }

    /**
     * Set log
     * @param $log
     */
    public function setLog($log)
    {
        $this->logs = Json::encode($log);
    }

    /**
     * Get IP
     */
    public function getIp()
    {
        return ArrayHelper::getValue(json_decode($this->logs, true), 'REMOTE_ADDR');
    }

    /**
     * Write one log record
     * @param $paymentId integer
     * @param $response array|string
     * @param $log array|string
     * @param $ip string
     * @return bool;
     */
    public static function log($paymentId, $response, $log, $ip)
    {
        $model = new static();
        $model->pid = $paymentId;
        $model->setResponse($response);
        $model->setLog($log);
        $model->ip = $ip;

        return $model->save(false);
    }
}
