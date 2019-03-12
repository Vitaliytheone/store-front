<?php

namespace superadmin\models\search;

use common\models\sommerces\CustomersCounters;
use superadmin\widgets\CountPagination;
use Yii;
use common\models\sommerces\Customers;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

/**
 * Class CustomersSearch
 * @package superadmin\models\search
 */
class CustomersSearch extends Customers
{
    public $countStores;
    public $countProjects;
    public $countChild;
    public $countDomains;
    public $countSslCerts;
    public $referrer_email;
    public $countGateways;

    /**
     * @var Pagination
     */
    public $pages;

    protected static $_customers;

    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all'
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
     * Build sql query
     * @param int|null $status
     * @return \yii\db\ActiveQuery
     */
    public function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();
        $id = Yii::$app->request->get('id');

        $customers = static::find();

        if ('all' === $status || null === $status) {
            if (empty($searchQuery)) {
                $customers->andWhere([
                    'customers.status' => [
                        Customers::STATUS_SUSPENDED,
                        Customers::STATUS_ACTIVE
                    ]
                ]);
            }
        } else {
            $customers->andWhere([
                'customers.status' => $status
            ]);
        }

        if (!empty($id)) {
            $customers->andWhere([
                'customers.id' => $id
            ]);
        }

        if (!empty($searchQuery)) {
            $customers->andFilterWhere([
                'or',
                ['=', 'customers.id', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        return $customers;
    }

    /**
     * @param $status
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function getCustomers($status)
    {
        return $this->buildQuery($status)
            ->select([
                'customers.*',
                'counters.stores AS countStores',
                'counters.domains AS countDomains',
                'counters.ssl_certs AS countSslCerts',
            ])
            ->leftJoin(['counters' => CustomersCounters::tableName()], 'counters.customer_id = customers.id')
            ->orderBy(['customers.id' => SORT_DESC])
            ->groupBy('customers.id')
            ->offset($this->pages->offset)
            ->limit($this->pages->limit)
            ->all();
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');

        $countQuery = $this->buildQuery($status)->count();
        //$this->setAllPageLabel();
        $pageSize = $this->getPageSize();
        if ($pageSize == 'all') {
            $pageSize = $countQuery;
        }
        $pages = new Pagination(['totalCount' => $countQuery, 'pageSize' => $pageSize]);
        $this->pages = $pages;

        return [
            'models' => $this->getCustomers($status),
            'pages' => $this->pages,
        ];
    }

    /**
     * @param $email
     * @param $status
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function ajaxSelectSearch($email, $status) {
        if ($status === 'all') {
            return Customers::find()
                ->andFilterWhere(['like', 'email', trim($email)])
                ->limit(10)->asArray()->all();
        }

        return Customers::find()
            ->andWhere(['status' => $status])
            ->andFilterWhere(['like', 'email', trim($email)])
            ->limit(10)->asArray()->all();
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            'all' => Yii::t('app/superadmin', 'customers.list.tab_all', [
                'count' => $this->buildQuery()->count()
            ]),
            Customers::STATUS_ACTIVE => Yii::t('app/superadmin', 'customers.list.tab_active', [
                'count' => $this->buildQuery(Customers::STATUS_ACTIVE)->count()
            ]),
            Customers::STATUS_SUSPENDED => Yii::t('app/superadmin', 'customers.list.tab_suspended', [
                'count' => $this->buildQuery(Customers::STATUS_SUSPENDED)->count()
            ]),
        ];
    }
}
