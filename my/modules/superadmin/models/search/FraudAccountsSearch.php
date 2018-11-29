<?php

namespace superadmin\models\search;


use common\models\panels\PaypalFraudAccounts;
use superadmin\widgets\CountPagination;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class FraudAccountsSearch
 * @package superadmin\models\search
 */
class FraudAccountsSearch extends Model
{
    use SearchTrait;

    /**
     * Set value of page size
     */
    public function getPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        return array_key_exists($pageSize, CountPagination::$pageSizeList) ? $pageSize : 100;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [
            'page_size' => ArrayHelper::getValue($this->params, 'page_size', null),
        ];
    }

    /**
     * Build query
     * @return Query
     */
    private function buildQuery(): Query
    {
        $query = (new Query())
            ->select('*')
            ->from(DB_PANELS . '.paypal_fraud_accounts');

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

        $accounts = $this->buildQuery()
            ->orderBy(['id' => SORT_DESC])
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return [
            'models' => $this->prepareData($accounts),
            'pages' => $pages,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $item) {

            $result[$key] = $item;
            $result[$key]['fraud_risk'] = PaypalFraudAccounts::getRiskName($item['fraud_risk']);
            $result[$key]['payer_status'] = PaypalFraudAccounts::getStatusName($item['payer_status']);
            $result[$key]['created_at'] = PaypalFraudAccounts::formatDate($item['created_at'], 'php:Y-m-d H:i:s');
            $result[$key]['updated_at'] = PaypalFraudAccounts::formatDate($item['updated_at'], 'php:Y-m-d H:i:s');
        }

        return $result;
    }
}