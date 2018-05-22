<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\Project;
use common\models\store\Orders;
use common\models\stores\Stores;
use my\helpers\DomainsHelper;
use common\models\panels\InvoiceDetails;
use common\models\panels\PaymentGateway;
use common\models\panels\Payments;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentsSearch
 * @package my\modules\superadmin\models\search
 */
class PaymentsSearch extends Payments {

    public $domain;

    protected $pageSize = 100;

    /**
     * @var array - methods
     */
    protected static $_methods;


    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : null,
            'mode' => isset($this->params['mode']) && is_numeric($this->params['mode']) ? (int)$this->params['mode'] : null,
            'method' => isset($this->params['method']) && is_numeric($this->params['method']) ? (int)$this->params['method'] : null,
        ];
    }

    /**
     * Build sql query
     * @param int $status
     * @param int $mode
     * @param int $method
     * @return ActiveRecord
     */
    public function buildQuery($status = null, $mode = null, $method = null)
    {
        $searchQuery = $this->getQuery();

        $payments = static::find();

        if (null !== $status && '' !== $status) {
            $payments->andWhere([
                'payments.status' => $status
            ]);
        }

        if (null !== $mode && '' !== $mode) {
            $payments->andWhere([
                'payments.mode' => $mode
            ]);
        }

        if (null !== $method && '' !== $method) {
            $payments->andWhere([
                'payments.type' => $method
            ]);
        }

        if (!empty($searchQuery)) {
            $payments->andFilterWhere([
                'or',
                ['=', 'payments.id', $searchQuery],
                ['like', 'payments.comment', $searchQuery],
                ['like', 'payments.transaction_id', $searchQuery],
                ['like', 'orders.domain', $searchQuery],
                ['like', 'project.site', $searchQuery],
            ]);
        }

        return $payments;
    }

    /**
     * Add join query
     * @param $query
     * @return mixed
     */
    protected function addDomainJoinQuery($query)
    {
        $query->leftJoin(['invoice_details' => InvoiceDetails::tableName()], 'invoice_details.invoice_id = payments.iid');
        $query->leftJoin(['orders' => Orders::tableName()], 'orders.id = invoice_details.item_id AND orders.domain IS NOT NULL AND invoice_details.item IN (' . implode(",", [
                InvoiceDetails::ITEM_BUY_PANEL,
                InvoiceDetails::ITEM_BUY_CHILD_PANEL,
                InvoiceDetails::ITEM_BUY_SSL,
                InvoiceDetails::ITEM_BUY_DOMAIN,
                InvoiceDetails::ITEM_BUY_STORE,
                InvoiceDetails::ITEM_BUY_TRIAL_STORE,
                InvoiceDetails::ITEM_PROLONGATION_STORE,
                InvoiceDetails::ITEM_PROLONGATION_SSL,
                InvoiceDetails::ITEM_PROLONGATION_DOMAIN,
            ]) . ')'
        );
        $query->leftJoin(['project' => Project::tableName()], 'project.id = invoice_details.item_id AND invoice_details.item IN (' . implode(",", [
            InvoiceDetails::ITEM_PROLONGATION_PANEL,
            InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL,
        ]) . ')');

        $query->leftJoin(['store' => Stores::tableName()], 'store.id = invoice_details.item_id AND invoice_details.item IN (' . implode(",", [
            InvoiceDetails::ITEM_PROLONGATION_STORE,
        ]) . ')');

        return $query;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);
        $mode = ArrayHelper::getValue($this->params, 'mode', null);
        $method = ArrayHelper::getValue($this->params, 'method', null);

        $query = clone $this->buildQuery($status, $mode, $method);

        $pages = new Pagination(['totalCount' => $this->count($status, $mode, $method)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $query = $this->addDomainJoinQuery($query);

        $payments = $query->select([
                'payments.*',
                'COALESCE(orders.domain, project.site, store.domain) as domain'
            ])
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->groupBy('payments.id')
            ->orderBy([
                'payments.id' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $payments,
            'pages' => $pages,
        ];
    }

    /**
     * @return array
     */
    public static function getMethods()
    {
        if (null == static::$_methods) {
            static::$_methods = [];
            foreach((new Query())
                ->select([
                    'pgid',
                    'name'
                ])
                ->from('payment_gateway')
                ->andWhere([
                    'pid' => '-1'
                ])
                ->all() as $method) {
                static::$_methods[$method['pgid']] = $method;
            }
        }

        return static::$_methods;
    }

    /**
     * Get count panels by type
     * @param int $status
     * @param int $mode
     * @param int $method
     * @return int
     */
    public function count($status = null, $mode = null, $method = null)
    {
        $searchQuery = $this->getQuery();
        $query = clone $this->buildQuery($status, $mode, $method);

        if (!empty($searchQuery)) {
            $query = $this->addDomainJoinQuery($query);
        }

        return (int)$query->select('COUNT(*)')->scalar();
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => Yii::t('app/superadmin', 'payments.list.navs_all', [
                'count' => $this->count()
            ]),
            Payments::STATUS_PENDING => Yii::t('app/superadmin', 'payments.list.navs_pending', [
                'count' => $this->count(Payments::STATUS_PENDING)
            ]),
            Payments::STATUS_COMPLETED => Yii::t('app/superadmin', 'payments.list.navs_completed', [
                'count' => $this->count(Payments::STATUS_COMPLETED)
            ]),
            Payments::STATUS_VERIFICATION => Yii::t('app/superadmin', 'payments.list.navs_verification', [
                'count' => $this->count(Payments::STATUS_VERIFICATION)
            ]),
            Payments::STATUS_WAIT => Yii::t('app/superadmin', 'payments.list.navs_wait', [
                'count' => $this->count(Payments::STATUS_WAIT)
            ]),
            Payments::STATUS_REVIEW => Yii::t('app/superadmin', 'payments.list.navs_review', [
                'count' => $this->count(Payments::STATUS_REVIEW)
            ]),
            Payments::STATUS_FAIL => Yii::t('app/superadmin', 'payments.list.navs_fail', [
                'count' => $this->count(Payments::STATUS_FAIL)
            ]),
            Payments::STATUS_REFUNDED => Yii::t('app/superadmin', 'payments.list.navs_refunded', [
                'count' => $this->count(Payments::STATUS_REFUNDED)
            ]),
            Payments::STATUS_UNVERIFIED => Yii::t('app/superadmin', 'payments.list.navs_unverified', [
                'count' => $this->count(Payments::STATUS_UNVERIFIED)
            ]),
            Payments::STATUS_EXPIRED => Yii::t('app/superadmin', 'payments.list.navs_expired', [
                'count' => $this->count(Payments::STATUS_EXPIRED)
            ]),
        ];
    }

    /**
     * Get aggregated modes
     * @return array
     */
    public function getAggregatedModes()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);
        $method = isset($this->params['method']) && is_numeric($this->params['method']) ? $this->params['method'] : null;
        $modes = [
            null => Yii::t('app/superadmin', 'payments.list.navs_mode_all', [
                'count' => $this->count($status, null, $method)
            ]),
            Payments::MODE_MANUAL => Yii::t('app/superadmin', 'payments.list.navs_mode_manual', [
                'count' => $this->count($status, Payments::MODE_MANUAL, $method)
            ]),
            Payments::MODE_AUTO => Yii::t('app/superadmin', 'payments.list.navs_mode_auto', [
                'count' => $this->count($status, Payments::MODE_AUTO, $method)
            ]),
        ];

        return $modes;
    }

    /**
     * Get aggregated methods
     * @return array
     */
    public function getAggregatedMethods()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);
        $mode = isset($this->params['mode']) && is_numeric($this->params['mode']) ? $this->params['mode'] : null;

        $returnMethods = [
            null => Yii::t('app/superadmin', 'payments.list.navs_method_all', [
                'count' => $this->count($status, $mode)
            ])
        ];

        $methods = static::getMethods();

        foreach ($methods as $method) {
            $returnMethods[$method['pgid']] = $method['name'] . ' (' . $this->count($status, $mode, $method['pgid']) . ')';
        }

        $returnMethods[0] = Yii::t('app/superadmin', 'payments.list.navs_method_other', [
            'count' => $this->count($status, $mode, 0)
        ]);

        return $returnMethods;
    }

    /**
     * Get payment method name
     * @return mixed
     */
    public function getMethodName()
    {
        $methods = static::getMethods();

        return ArrayHelper::getValue(ArrayHelper::getValue($methods, $this->type), 'name', Yii::t('app', 'payment_gateway.method.other'));
    }

    /**
     * Get domain name
     * @return string
     */
    public function getDomain()
    {
        return $this->domain ? DomainsHelper::idnToUtf8($this->domain) : '';
    }
}