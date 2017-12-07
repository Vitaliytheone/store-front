<?php

namespace frontend\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\db\Query;
use console\components\panelchecker\PanelcheckerComponent;

/**
 * Class LevopanelsSearch
 * @property string table
 * @package frontend\modules\admin\models\search
 */
class LevopanelsSearch extends Model
{
    const DB_NAME = 'checker';
    const PANELS_TABLE_NAME = 'levopanel';

    public function getTable()
    {
        return self::DB_NAME . '.' . self::PANELS_TABLE_NAME;
    }

    /**
     * Return array of panels data
     * @return array
     */
    public function searchPanels()
    {
        $panels = (new Query())
            ->select(['id','domain', 'server_ip', 'status', 'details', 'created_at', 'updated_at'])
            ->from($this->table)
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $formatter = Yii::$app->formatter;

        array_walk($panels, function(&$panel) use ($formatter){
            $panel['status_name'] = PanelcheckerComponent::getStatusName($panel['status']);
            $panel['created_at_f'] = $formatter->asDatetime($panel['created_at'], 'yyyy-MM-dd HH:mm:ss');
            $panel['updated_at_f'] = $formatter->asDatetime($panel['updated_at'], 'yyyy-MM-dd HH:mm:ss');
        });

        return $panels;
    }
}