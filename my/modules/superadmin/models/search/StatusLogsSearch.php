<?php
namespace my\modules\superadmin\models\search;

use Yii;
use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\Logs;
use common\models\panels\Project;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class StatusLogsSearch
 * @package my\modules\superadmin\models\search
 */
class StatusLogsSearch
{

    use UnixTimeFormatTrait;

    const PAGE_SIZE = 100;

    private $_logsTable;
    private $_projectTable;

    /** @var $_dataProvider ActiveDataProvider */
    private $_dataProvider;

    public function __construct()
    {
        $this->_logsTable = Logs::tableName();
        $this->_projectTable = Project::tableName();
    }

    /**
     * Search
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = (new Query())
            ->select([
                'id', 'panel_id', 'data', 'type', 'created_at',
            ])
            ->from($this->_logsTable)
            ->indexBy('id')
            ->orderBy([
                'id' => SORT_DESC,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => static::PAGE_SIZE,
            ],
        ]);

        $this->_dataProvider = $dataProvider;

        return $this->_dataProvider;
    }

    /**
     * Return found models with additional data for view
     * @return array
     */
    public function getModelsForView()
    {
        if (!$this->_dataProvider instanceof ActiveDataProvider) {
            return [];
        }

        $models = $this->_dataProvider->getModels();
        $panelsIds = array_column($models, 'panel_id');
        $panelStatuses = Logs::getTypes();

        $panelsData = (new Query())
            ->select(['id', 'site', 'name'])
            ->from($this->_projectTable)
            ->indexBy('id')
            ->where(['in', 'id', $panelsIds])
            ->all();

        array_walk($models, function(&$model) use ($panelsData, $panelStatuses){

            $panelStatusName = ArrayHelper::getValue($panelStatuses, $model['type']);

            $modelData = $panelsData[$model['panel_id']];
            $model = [
                'id' => $model['id'],
                'panel_id' => $model['panel_id'],
                'panel' => ArrayHelper::getValue($modelData, 'site'),
                'status' => $panelStatusName,
                'date' => static::formatDate($model['created_at']),
            ];
        });

        return $models;
    }

}