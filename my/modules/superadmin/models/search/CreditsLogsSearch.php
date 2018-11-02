<?php
namespace superadmin\models\search;

use common\models\panels\SuperAdmin;
use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\SuperCreditsLog;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class CreditsLogsSearch
 * @package superadmin\models\search
 */
class CreditsLogsSearch
{
    use UnixTimeFormatTrait;

    const PAGE_SIZE = 100;

    private $_logsTable;
    private $_superadminTable;

    /** @var $_dataProvider ActiveDataProvider */
    private $_dataProvider;

    public function __construct()
    {
        $this->_logsTable = SuperCreditsLog::tableName();
        $this->_superadminTable = SuperAdmin::tableName();
    }

    /**
     * Search
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = (new Query())
            ->select([
                'lt.id id', 'lt.super_admin_id admin_id', 'lt.invoice_id invoice_id', 'lt.credit credit', 'lt.memo memo', 'lt.created_at created_at',
                'sat.username admin_name'
            ])
            ->from(['lt' => $this->_logsTable])
            ->leftJoin(['sat' => $this->_superadminTable], 'sat.id = lt.super_admin_id')
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

        array_walk($models, function(&$model) {
            $model = [
                'id' => $model['id'],
                'admin_name' => $model['admin_name'],
                'invoice_id' => $model['invoice_id'],
                'credit' => $model['credit'],
                'memo' => $model['memo'],
                'created_at' => static::formatDate($model['created_at']),
            ];
        });

        return $models;
    }

}