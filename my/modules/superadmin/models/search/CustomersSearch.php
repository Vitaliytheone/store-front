<?php
namespace my\modules\superadmin\models\search;

use common\models\stores\Stores;
use Yii;
use common\models\panels\Customers;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

/**
 * Class CustomersSearch
 * @package my\modules\superadmin\models\search
 */
class CustomersSearch extends Customers {

    public $countStores;
    public $countProjects;
    public $countChild;
    public $countDomains;
    public $countSslCerts;

    /**
     * @var Pagination
     */
    public $pages;

    protected static $_customers;

    /**
     * @var array
     */
    public static $pageSizeList = [
        100 => 100,
        500 => 500,
        1000 => 1000,
        5000 => 5000,
    ];

    use SearchTrait;

    /**
     * Set label for 'All'
     */
    private function setAllPageLabel()
    {
        static::$pageSizeList['all'] = Yii::t('app/superadmin', 'customers.pagination.all');
    }

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
        return array_key_exists($pageSize, static::$pageSizeList) ? $pageSize : 100;
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
        return $customers = $this->buildQuery($status)
            ->select([
                'customers.*',
                'COUNT(DISTINCT stores.id) AS countStores',
                'COUNT(DISTINCT project.id) AS countProjects',
                'COUNT(DISTINCT child_project.id) AS countChild',
                'COUNT(DISTINCT domains.id) AS countDomains',
                'COUNT(DISTINCT ssl_cert.id) AS countSslCerts'
            ])->leftJoin(['stores' => Stores::tableName()], 'stores.customer_id = customers.id', [':projectChildPanel' => 0])
            ->leftJoin('project', 'project.cid = customers.id AND project.child_panel = :projectChildPanel', [':projectChildPanel' => 0])
            ->leftJoin('project AS child_project', 'child_project.cid = customers.id AND child_project.child_panel = :childPanel', [':childPanel' => 1])
            ->leftJoin('domains', 'domains.customer_id = customers.id')
            ->leftJoin('ssl_cert', 'ssl_cert.cid = customers.id')
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
        $this->setAllPageLabel();
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
