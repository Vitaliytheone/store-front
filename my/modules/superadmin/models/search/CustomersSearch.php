<?php
namespace my\modules\superadmin\models\search;

use common\models\stores\Stores;
use Yii;
use common\models\panels\Customers;
use yii\helpers\ArrayHelper;

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
            'status' => isset($this->params['status']) ? $this->params['status'] : Customers::STATUS_ACTIVE
        ];
    }

    /**
     * Build sql query
     * @param int $status
     * @return $this
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

        $customers->select([
            'customers.*',
            'COUNT(DISTINCT stores.id) AS countStores',
            'COUNT(DISTINCT project.id) AS countProjects',
            'COUNT(DISTINCT child_project.id) AS countChild',
            'COUNT(DISTINCT domains.id) AS countDomains',
            'COUNT(DISTINCT ssl_cert.id) AS countSslCerts'
        ]);

        $customers->leftJoin(['stores' => Stores::tableName()], 'stores.customer_id = customers.id', [
            ':projectChildPanel' => 0
        ]);
        $customers->leftJoin('project', 'project.cid = customers.id AND project.child_panel = :projectChildPanel', [
            ':projectChildPanel' => 0
        ]);
        $customers->leftJoin('project AS child_project', 'child_project.cid = customers.id AND child_project.child_panel = :childPanel', [
            ':childPanel' => 1
        ]);
        $customers->leftJoin('domains', 'domains.customer_id = customers.id');
        $customers->leftJoin('ssl_cert', 'ssl_cert.cid = customers.id');

        $customers->orderBy([
            'customers.id' => SORT_DESC
        ])->groupBy('customers.id');

        return $customers;
    }

    /**
     * Get customers
     * @param string|integer $status
     * @return array
     */
    protected function getCustomers($status)
    {
        if (empty(static::$_customers)) {
            static::$_customers = $this->buildQuery()->all();
        }

        if ('all' === $status || null === $status) {
            return static::$_customers;
        }

        $customers = [];

        foreach (static::$_customers as $customer) {
            if ($customer->status != $status) {
                continue;
            }

            $customers[] = $customer;
        }

        return $customers;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', Customers::STATUS_ACTIVE);

        return [
            'models' => $this->getCustomers($status)
        ];
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            'all' => 'All (' . count($this->getCustomers('all')) . ')',
            Customers::STATUS_ACTIVE => 'Active (' . count($this->getCustomers(Customers::STATUS_ACTIVE)) . ')',
            Customers::STATUS_SUSPENDED => 'Suspended (' . count($this->getCustomers(Customers::STATUS_SUSPENDED)) . ')',
        ];
    }
}