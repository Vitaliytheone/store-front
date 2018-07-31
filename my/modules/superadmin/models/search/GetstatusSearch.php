<?php

namespace my\modules\superadmin\models\search;

use common\models\panels\Getstatus;
use common\models\panels\AdditionalServices;
use Yii;
use yii\base\DynamicModel;
use yii\db\Query;

/**
 * Class GetstatusSearch
 * @package my\modules\superadmin\models\search
 */
class GetstatusSearch extends Getstatus
{

    private $_data = [];

    use SearchTrait;

    /**
     * @param $option string
     * @return string
     */
    private function getDefaultDate($option)
    {
        return $option == 'to' ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', time() - 1 * 60 * 60);
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
     * Set json by response from url
     */
    private function getData()
    {
        $datetime = $this->getDatetime();

        $createUrl = '?from=' . strtotime($datetime['from']) . '&to=' . strtotime($datetime['to']);

        $url = Yii::$app->params['getstatus_info_url'] . $createUrl;

        $data = file_get_contents($url);
        $data = $data ? json_decode($data, true) : [];

        if (json_last_error() === JSON_ERROR_NONE) {
            return $this->_data = $data;
        }

        return $this->_data = [];
    }

    /**
     * Get the date-time parameters
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getDatetime()
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

        return $datetime;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        $data = $this->getData();

        $statuses = AdditionalServices::find()
            ->select(['res', 'name'])
            ->where(['additional_services.res' => array_keys($data)])
            ->groupBy('res')
            ->all();

        $countsList = array();

        $counts = (new Query())
            ->select(['res', 'COUNT(*) AS count'])
            ->from('getstatus')
            ->groupBy('res')
            ->all();

        foreach ($counts as $count) {
            $countsList[$count['res']] = $count['count'];
        }

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
