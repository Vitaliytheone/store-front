<?php

namespace superadmin\models\search;


use common\models\panels\PaypalFraudAccounts;
use superadmin\widgets\CountPagination;
use Yii;
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

    const SEARCH_TYPE_PAYER_ID = 'payer_id';
    const SEARCH_TYPE_PAYER_EMAIL = 'payer_email';
    const SEARCH_TYPE_PAYER_FIRST_NAME = 'firstname';
    const SEARCH_TYPE_PAYER_LAST_NAME = 'lastname';

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'query' => isset($this->params['query']) ? trim($this->params['query']) : null,
            'search_type' =>
                isset($this->params['search_type']) && array_key_exists(trim($this->params['search_type']), static::getSearchTypes()) ?
                    trim($this->params['search_type']) :
                    null,
            'page_size' => isset($this->params['page_size']) ? $this->params['page_size'] : null,
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
     * @param string|null $searchFilter
     * @param string|null $searchType
     * @return Query
     */
    private function buildQuery(string $searchFilter = null, string $searchType = null): Query
    {
        $query = (new Query())
            ->select('*')
            ->from(DB_PANELS . '.paypal_fraud_accounts');

        if (isset($searchFilter)) {
            if (!isset($searchType) || $searchType == 'payer_id' || $searchType == 'payer_email') {
                $query->andFilterWhere([$searchType => $searchFilter]);
            } else {
                $query->andFilterWhere(['like', $searchType, $searchFilter]);
            }
        }

        return $query;
    }

    /**
     * @return array
     */
    public function search(): array
    {
        $searchParams = $this->getFilters();

        $model = $this->buildQuery($searchParams['query'], $searchParams['search_type']);

        $countQuery = $model->count();
        $pageSize = $this->getPageSize();
        if ($pageSize == 'all') {
            $pageSize = $countQuery;
        }
        $pages = new Pagination(['totalCount' => $countQuery, 'pageSize' => $pageSize]);

        $accounts = $model->orderBy(['id' => SORT_DESC])
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
    /**
     * Get search types
     * @return array
     */
    public static function getSearchTypes(): array
    {
        return [
            static::SEARCH_TYPE_PAYER_ID => Yii::t('app/superadmin', 'fraud_payments.search_type.payer_id'),
            static::SEARCH_TYPE_PAYER_EMAIL => Yii::t('app/superadmin', 'fraud_payments.search_type.payer_email'),
            static::SEARCH_TYPE_PAYER_LAST_NAME => Yii::t('app/superadmin', 'fraud_payments.search_type.payer_lastname'),
            static::SEARCH_TYPE_PAYER_FIRST_NAME => Yii::t('app/superadmin', 'fraud_payments.search_type.payer_firstname'),
        ];
    }
}