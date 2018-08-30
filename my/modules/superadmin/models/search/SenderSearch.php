<?php
/**
 * Created by PhpStorm.
 * User: vitalij.z
 * Date: 02.08.2018
 * Time: 16:25
 */

namespace my\modules\superadmin\models\search;


use common\models\panels\SenderLog;
use yii\db\Query;
use Yii;
use yii\base\DynamicModel;

/**
 * Class SenderSearch
 * @package my\modules\superadmin\models\search
 */
class SenderSearch extends SenderLog
{
    public $from;
    public $to;

    private $_correctiveTime;

    use SearchTrait;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->_correctiveTime = Yii::$app->params['time'];
    }

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        $this->from = isset($this->params['from']) ? $this->params['from'] : $this->getDefaultDate('from');
        $this->to = isset($this->params['to']) ? $this->params['to'] : $this->getDefaultDate('to');

        return [
            'from' => $this->from,
            'to' => $this->to
        ];
    }

    /**
     * @param $option string
     * @return string
     */
    private function getDefaultDate($option)
    {
        return $option == 'to' ? date('Y-m-d H:i:s', time() + $this->_correctiveTime) : date('Y-m-d H:i:s', time() + $this->_correctiveTime - 24 * 60 * 60);
    }

    /**
     * Get senders
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function search()
    {
        $datetime = $this->getDateTime(false);

        $model = (new Query())
            ->select([
                'sender_log.id',
                'sender_log.provider_id',
                'sender_log.send_method',
                'additional_services.name as provider',
                'COUNT(sender_log.id) AS all_status',
                'COUNT(good.id) AS good',
                'COUNT(error.id) AS error',
                'COUNT(curl_error.id) AS curl_error'
            ])->from('sender_log')
            ->leftJoin('additional_services', 'additional_services.res = sender_log.provider_id')
            ->leftJoin('sender_log AS good', 'good.id = sender_log.id AND good.status = ' . SenderLog::STATUS_SUCCESS)
            ->leftJoin('sender_log AS error', 'error.id = sender_log.id AND error.status = ' . SenderLog::STATUS_ERROR)
            ->leftJoin('sender_log AS curl_error', 'curl_error.id = sender_log.id AND curl_error.status = ' . SenderLog::STATUS_CURL_ERROR)
            ->where('sender_log.created_at > ' . strtotime($datetime['from']))
            ->andWhere('sender_log.created_at < ' . strtotime($datetime['to']))
            ->groupBy(['provider_id'])
        ->all();

        $senders =  $this->prepareData($model);

        return [
            'senders' => $senders,
            'total' => $this->getTotals($senders)
        ];

    }

    /**
     * @param $senders array
     * @return array
     */
    private function getTotals(array $senders): array
    {
        $totals = [
            'all' => 0,
            'good' => 0,
            'error' => 0,
            'curl_error' => 0,
        ];

        foreach ($senders as $sender) {
            $totals['all'] += $sender['all_status'];
            $totals['good'] += $sender['good'];
            $totals['error'] += $sender['error'];
            $totals['curl_error'] += $sender['curl_error'];
        }

        return $totals;
    }

    /**
     * Get date-time
     * @param bool $render
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatetime($render = true)
    {
        $datetime = $this->getParams();

        $model = new DynamicModel($datetime);
        $model->addRule(['from','to'], 'trim')->validate();
        $model->addRule(['from','to'], 'date', ['format' => 'php:Y-m-d H:i:s'])->validate();
        $model->addRule(['from','to'], 'required')->validate();
        $model->addRule(['from'], function() use ($model) {
            if (strtotime($model->from) >= strtotime($this->getParams()['to'])) {
                $model->addError($model->from);
            }
        })->validate();
        $model->addRule(['to'], function() use ($model) {
            if (strtotime($model->to) <= strtotime($this->getParams()['from'])) {
                $model->addError($model->to);
            }
        })->validate();

        if ($model->hasErrors()) {
            $datetime['to'] = $this->getDefaultDate('to');
            $datetime['from'] = $this->getDefaultDate('from');
        }

        /* If method is used for render the data add params['time'] */
        if ($render == true) {
            return $datetime;
        }

        /* Take away params['time'] if data receive from the user */
        $datetime['to'] = date('Y-m-d H:i:s', strtotime($datetime['to']) - $this->_correctiveTime);
        $datetime['from'] = date('Y-m-d H:i:s', strtotime($datetime['from']) - $this->_correctiveTime);

        return $datetime;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function prepareData($data)
    {
        foreach ($data as $key => $sender) {
            $data[$key]['send_method'] = SenderLog::getSendMethodName($sender['send_method']);
        }

        return $data;
    }
}
