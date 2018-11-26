<?php

namespace superadmin\models\search;


use common\models\panels\PaypalFraudIncidents;
use common\models\panels\Project;
use superadmin\widgets\CountPagination;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class FraudIncidentsSerach
 * @package superadmin\models\search
 */
class FraudIncidentsSerach extends Model
{
    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams(): array
    {
        return [
            'page_size' => ArrayHelper::getValue($this->params, 'page_size', null),
        ];
    }

    /**
     * Set value of page size
     */
    public function getPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        return array_key_exists($pageSize, CountPagination::$pageSizeList) ? $pageSize : 100;
    }

    /**
     * @return Query
     */
    private function buildQuery(): Query
    {
        $query = (new Query())
            ->select('*')
            ->from(DB_PANELS . '.paypal_fraud_incidents');

        return $query;
    }

    /**
     * @return array
     */
    public function search(): array
    {
        $countQuery = $this->buildQuery()->count();
        $pageSize = $this->getPageSize();
        if ($pageSize == 'all') {
            $pageSize = $countQuery;
        }
        $pages = new Pagination(['totalCount' => $countQuery, 'pageSize' => $pageSize]);

        $model = $this->buildQuery();
        $model->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC]);

        return [
            'models' => $this->prepareData($model->all()),
            'pages' => $pages,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        $result = [];
        $panels = Project::find()->indexBy('id')->all();

        foreach ($data as $key => $item) {
            $panel = $panels[$item['panel_id']];
            $result[$key] = [
                'id' => $item['id'],
                'panel_domain' => $panel->site,
                'panel_id' => $item['panel_id'],
                'is_child' => $panel->child_panel,
                'payment_id' => $item['payment_id'],
                'fraud_risk' => PaypalFraudIncidents::getRiskName($item['fraud_risk']),
                'fraud_reason' => PaypalFraudIncidents::getReasonName($item['fraud_reason']),
                'balance_added' => PaypalFraudIncidents::getBalanceName($item['balance_added']),
                'created_at' => PaypalFraudIncidents::formatDate($item['created_at'], 'php:Y-m-d H:i:s'),
            ];
        }

        return $result;
    }
}
