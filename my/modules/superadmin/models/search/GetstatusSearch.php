<?php

namespace my\modules\superadmin\models\search;

use common\models\panels\Getstatus;
use common\models\panels\AdditionalServices;
use Yii;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class GetstatusSearch
 * @package my\modules\superadmin\models\search
 */
class GetstatusSearch extends Getstatus
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @var array
     */
    private $_data = [];

    private $_correctiveTime;

    use SearchTrait;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->_correctiveTime = Yii::$app->params['time'];

        $this->_url = Yii::$app->params['getstatus_info_url'];
    }

    /**
     * @param $option string
     * @return string
     */
    private function getDefaultDate($option)
    {
        return $option == 'to' ? date('Y-m-d H:i:s', time() + $this->_correctiveTime) : date('Y-m-d H:i:s', time() + $this->_correctiveTime - 1 * 60 * 60);
    }

    /**
     * Set the date-time parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'to' => isset($this->params['to']) ? $this->params['to'] : $this->getDefaultDate('to'),
            'from' => isset($this->params['from']) ? $this->params['from'] : $this->getDefaultDate('from')
        ];
    }

    /**
     * Get data
     *
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    private function getData()
    {
        if (!empty($this->_data) || empty($this->_url)) {
            return $this->_data;
        }

        $datetime = $this->getDatetime(false);

        $url = $this->_url . '?from=' . strtotime($datetime['from']) . '&to=' . strtotime($datetime['to']);

        $data = @file_get_contents($url);
        $data = $data ? json_decode($data, true) : [];

        if (json_last_error() === JSON_ERROR_NONE) {
            return $this->_data = $data;
        }

        return $this->_data = [];
    }

    /**
     * Get the date-time parameters
     * @param $render bool
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatetime($render = true)
    {
        $datetime = $this->getParams();

        $model = DynamicModel::validateData($datetime, [
            [['from', 'to'], 'trim'],
            [['from', 'to'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['from', 'to'], 'required'],
        ]);

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
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getStatuses()
    {
        $data = $this->getData();
        
        if (empty($data)) {
            return [];
        }

        $statuses = AdditionalServices::find()
            ->select(['res', 'name'])
            ->where(['additional_services.res' => array_keys($data)])
            ->groupBy('res')
            ->all();

        $countsList = (new Query())
            ->select(['res', 'COUNT(*) AS count'])
            ->from('getstatus')
            ->groupBy('res')
            ->all();
        $countsList = ArrayHelper::map($countsList, 'res', 'count');

        for ($i = 0; $i < count($data); $i++ ) {
            $data[$statuses[$i]->res]['provider'] = $statuses[$i]->name;
            $data[$statuses[$i]->res]['all_orders'] = isset($countsList[$statuses[$i]->res]) ? $countsList[$statuses[$i]->res] : 0;
            $data[$statuses[$i]->res]['good'] = $data[$statuses[$i]->res]['requests'] - $data[$statuses[$i]->res]['status_error'] - $data[$statuses[$i]->res]['curl_error'];
            $data[$statuses[$i]->res]['avg'] = round($data[$statuses[$i]->res]['avg'], 0);

            if (!isset($data[$i]['provider'])) {
                unset($data[$i]);
            }
        }

        return $data;
    }

}
