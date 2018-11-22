<?php

namespace superadmin\models\search;


use common\models\panels\Project;
use superadmin\widgets\CountPagination;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;
use Yii;

/**
 * Class FraudPaymentsSearch
 * @package superadmin\models\search
 */
class FraudPaymentsSearch extends Model
{
    const SEARCH_TYPE_PAYER_ID = 'payer_id';
    const SEARCH_TYPE_PAYER_EMAIL = 'payer_email';
    const SEARCH_TYPE_PAYER_FIRST_NAME = 'firstname';
    const SEARCH_TYPE_PAYER_LAST_NAME = 'lastname';

    use SearchTrait;

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'query' => isset($this->params['query']) ? $this->params['query'] : null,
            'search_type' =>
                isset($this->params['search_type']) && array_key_exists($this->params['search_type'], static::getSearchTypes()) ?
                $this->params['search_type'] :
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
     * Build query
     * @param string|null $searchFilter
     * @param string|null $searchType
     * @return Query
     */
    private function buildQuery(string $searchFilter = null, string $searchType = null): Query
    {
        $query = (new Query())
            ->from(DB_PANELS . '.paypal_payments');

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

        $model->orderBy(['id' => SORT_DESC])
            ->offset($pages->offset)
            ->limit($pages->limit);

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

        foreach ($data as $key => $item) {
            $panel = Project::findOne(['id' => $item['panel_id']]);
            $result[$key] = [
                'id' => $item['id'],
                'panel' => $panel,
                'payment_id' => $item['payment_id'],
                'payer_id' => $item['payer_id'],
                'payer_email' => $item['payer_email'],
                'firstname' => $item['firstname'],
                'lastname' => $item['lastname'],
                'created_at' => date('Y-m-d H:i:s', $item['created_at']),
                'updated_at' => isset($item['updated_at']) ? date('Y-m-d H:i:s', $item['updated_at']) : '',
            ];
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