<?php

namespace superadmin\models\search;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\SuperToolsScanner;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PanelsScannerSearch
 * @package superadmin\models\search
 */
class PanelsScannerSearch extends Model
{
    use UnixTimeFormatTrait;

    private $_status;

    private $_panel;

    public function setPanel($panel) {
        $this->_panel = $panel;
    }

    public function getPanel()
    {
        return $this->_panel;
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
            ->select(['id', 'panel_id', 'domain', 'server_ip', 'status', 'details', 'created_at', 'updated_at'])
            ->from(SuperToolsScanner::tableName())
            ->andWhere([
                'panel' => $this->getPanel()
            ])
            ->orderBy(['id' => SORT_DESC]);

        if (in_array($status, SuperToolsScanner::$statuses)) {
            $query->andWhere(['status' => $status]);
        }

        $panels = $query->all();

        array_walk($panels, function(&$panel){
            $panel['status_name'] = SuperToolsScanner::getStatusName($panel['status']);
            $panel['created_at_f'] = static::formatDate($panel['created_at']);
            $panel['updated_at_f'] = static::formatDate($panel['updated_at']);
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
            ->from(SuperToolsScanner::tableName())
            ->andWhere([
                'panel' => $this->getPanel()
            ])
            ->groupBy(['status'])
            ->indexBy('status')
            ->all();

        return $counts;
    }

    /**
     * Return status buttons
     * @return array
     */
    public function getStatusButtons()
    {
        $buttons = [];

        $countsByStatus = $this->countsByStatus();

        array_push($buttons, [
            'status' => null,
            'title' => 'All',
            'count' => array_sum(array_column($countsByStatus, 'count')),
        ]);

        foreach (SuperToolsScanner::statusesLabels() as $status => $statusName) {

            $buttons[] = [
                'status' => (int)$status,
                'title' => $statusName,
                'count' => ArrayHelper::getValue($countsByStatus, "$status.count", 0),
            ];
        }

        return $buttons;
    }

}