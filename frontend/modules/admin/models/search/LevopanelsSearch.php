<?php

namespace frontend\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\db\Query;
use console\components\panelchecker\PanelcheckerComponent;
use frontend\modules\admin\components\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\UiHelper;

/**
 * Class LevopanelsSearch
 * @property string table
 * @package frontend\modules\admin\models\search
 */
class LevopanelsSearch extends Model
{
    private $_status;

    const DB_NAME = 'checker';
    const PANELS_TABLE_NAME = 'levopanel';

    public function getTable()
    {
        return self::DB_NAME . '.' . self::PANELS_TABLE_NAME;
    }

    /**
     * Return array of panels data
     * @param $status
     * @return array
     */
    public function searchPanels($status)
    {
        $this->_status = $status;

        $query = (new Query())
            ->select(['id','domain', 'server_ip', 'status', 'details', 'created_at', 'updated_at'])
            ->from($this->table)
            ->orderBy(['id' => SORT_ASC]);

        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $panels = $query->all();

        $formatter = Yii::$app->formatter;

        array_walk($panels, function(&$panel) use ($formatter){
            $panel['status_name'] = PanelcheckerComponent::getStatusName($panel['status']);
            $panel['created_at_f'] = $formatter->asDatetime($panel['created_at'], 'yyyy-MM-dd HH:mm:ss');
            $panel['updated_at_f'] = $formatter->asDatetime($panel['updated_at'], 'yyyy-MM-dd HH:mm:ss');
        });

        return $panels;
    }

    /**
     * Counts panels by `status`
     * @return array
     */
    public function countsByStatus()
    {
        $counts = (new Query())
            ->select(['status', 'COUNT(*) count'])
            ->from($this->table)
            ->groupBy(['status'])
            ->indexBy('status')
            ->all();

        return $counts;
    }


    public function getStatusButtons()
    {
        $buttons = [
            'all' => [
                'title' => null,
                'url' => null,
                'count' => null,
            ],
            PanelcheckerComponent::PANEL_STATUS_ACTIVE => [
            ],
            PanelcheckerComponent::PANEL_STATUS_FROZEN => [
            ],
            PanelcheckerComponent::PANEL_STATUS_PERFECTPANEL => [
            ],
            PanelcheckerComponent::PANEL_STATUS_NOT_RESOLVED => [
            ],
            PanelcheckerComponent::PANEL_STATUS_IP_NOT_LEVOPANEL => [
            ],
            PanelcheckerComponent::PANEL_STATUS_PARKING => [
            ],
            PanelcheckerComponent::PANEL_STATUS_OTHER => [
            ],
        ];

        $countsByStatus = $this->countsByStatus();

        array_walk($buttons, function(&$button, $status) use ($countsByStatus){

            if ($status === 'all') {
                $button['url'] = Url::toRoute('/levopanel');
                $button['count'] = array_sum(array_column($countsByStatus, 'count'));
            } else {
                $button['url'] = Url::current(['status' => $status]);
                $button['count'] = ArrayHelper::getValue($countsByStatus, "$status.count", 0);
            }

            $button['title'] = PanelcheckerComponent::getStatusName($status);
            $button['active'] = UiHelper::isFilterActive('status', $status);

        });

        return $buttons;
    }

}