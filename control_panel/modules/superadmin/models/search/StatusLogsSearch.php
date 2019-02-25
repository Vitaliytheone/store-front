<?php
namespace superadmin\models\search;

use common\helpers\ProjectHelper;
use common\models\common\ProjectInterface;
use common\models\stores\Stores;
use Yii;
use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\Logs;
use common\models\panels\Project;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class StatusLogsSearch
 * @package superadmin\models\search
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
        $query = Logs::find()
            ->select([
                '`logs`.*',
                'domain' => 'COALESCE(panel.site, store.domain)'
            ])
            ->leftJoin(['panel' => Project::tableName()], 'logs.project_type = :project_panel AND logs.panel_id = panel.id', [
                ':project_panel' => ProjectInterface::PROJECT_TYPE_PANEL
            ])
            ->leftJoin(['store' => Stores::tableName()], 'logs.project_type = :project_store AND logs.panel_id = store.id', [
                ':project_store' => ProjectInterface::PROJECT_TYPE_STORE
            ])
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->indexBy('id')
            ->asArray();

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

        $projectStatuses = Logs::getTypes();

        $models = $this->_dataProvider->getModels();

        array_walk($models, function(&$model) use ($projectStatuses){
            $model = [
                'id' => $model['id'],
                'project_type' => ProjectHelper::getProjectTypeName($model['project_type']),
                'domain' => ArrayHelper::getValue($model, 'domain'),
                'status' => ArrayHelper::getValue($projectStatuses, $model['type']),
                'date' => static::formatDate($model['created_at']),
            ];
        });

        return $models;
    }

}