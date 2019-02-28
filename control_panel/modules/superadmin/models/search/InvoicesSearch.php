<?php

namespace superadmin\models\search;

use control_panel\helpers\DomainsHelper;
use common\models\sommerces\InvoiceDetails;
use Yii;
use common\models\sommerces\Invoices;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class InvoicesSearch
 * @package superadmin\models\search
 */
class InvoicesSearch extends Invoices {

    public $email;
    public $domain;
    public $editTotal;

    protected $pageSize = 100;

    private $invoiceIdQuery = [];

    const SEARCH_TYPE_INVOICE_ID = 1;
    const SEARCH_TYPE_DOMAIN = 2;
    const SEARCH_TYPE_CUSTOMER = 3;

    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => isset($this->params['search_type']) && $this->params['search_type'] == static::SEARCH_TYPE_INVOICE_ID
                ? implode(',', $this->invoiceIdQuery) : $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : null,
            'search_type' => isset($this->params['search_type']) ? $this->params['search_type'] : null,
        ];
    }

    /**
     * Build sql query
     * @param int $status
     * @return Query
     */
    public function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();
        $searchType = isset($this->params['search_type']) && is_numeric($this->params['search_type']) ? $this->params['search_type'] : null;
        $id = ArrayHelper::getValue($this->params, 'id');

        $invoices = static::find();

        if (null !== $status && '' !== $status) {
            $invoices->andWhere([
                'invoices.status' => $status
            ]);
        }

        $invoices->leftJoin(DB_PANELS . '.invoice_details', 'invoice_details.invoice_id = invoices.id');
        $invoices->andWhere(['invoice_details.item' => InvoiceDetails::getSommerceOrderItems()]);

        if ($searchQuery && !empty($searchType)) {
            switch ($searchType) {
                case static::SEARCH_TYPE_INVOICE_ID:
                    $searchValues = array_unique(array_map(function($value) {return (int)trim($value);}, explode(',', (string)$searchQuery)));
                    $this->invoiceIdQuery = $searchValues;
                    $invoices->andWhere(['invoices.id' => $searchValues]);
                    break;
                case  static::SEARCH_TYPE_DOMAIN:
                    $invoices->andFilterWhere([
                        'or',
                        ['like', 'orders.domain', (string)$searchQuery],
                        ['like', 'stores.domain', (string)$searchQuery],
                        ['like', 'customers.email', (string)$searchQuery],
                    ]);
                    break;
                case static::SEARCH_TYPE_CUSTOMER:
                    $invoices->andFilterWhere([
                        'like', 'customer_email.email', (string)$searchQuery
                    ]);
                    break;
            }
        }

        if ($id) {
            $invoices->andWhere([
                'invoices.id' => $id
            ]);
        }

        return $invoices->groupBy('invoices.id');
    }

    /**
     * Add join query
     * @param Query $query
     * @return mixed
     */
    protected function addDomainJoinQuery($query)
    {
        $query->leftJoin(
            DB_PANELS . '.orders', 'orders.id = invoice_details.item_id AND orders.domain IS NOT NULL AND invoice_details.item IN (' . implode(",", InvoiceDetails::getSommerceOrderItems()) . ')'
        );
        $query->leftJoin(DB_SOMMERCES . '.stores', 'stores.id = invoice_details.item_id AND invoice_details.item IN (' . implode(",", [
                InvoiceDetails::ITEM_PROLONGATION_STORE,
        ]) . ')');
        $query->leftJoin(DB_PANELS . '.customers', 'customers.id = invoice_details.item_id AND invoice_details.item = ' . InvoiceDetails::ITEM_CUSTOM_CUSTOMER);
        $query->leftJoin(DB_PANELS . '.customers as customer_email', 'customer_email.id = invoices.cid');

        return $query;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);

        $pages = new Pagination(['totalCount' => $this->count($status)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $query = clone $this->buildQuery($status);

        $query = $this->addDomainJoinQuery($query);

        $invoices = $query->select([
                'invoices.*',
                'customer_email.email as email',
                'COALESCE(orders.domain, stores.domain, customers.email) as domain',
                'IF (invoice_details.item = ' . InvoiceDetails::ITEM_PROLONGATION_PANEL . ', 1, 0) as editTotal'
            ])->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'invoices.id' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $this->canEditTotal($invoices),
            'pages' => $pages,
        ];
    }

    /**
     * @param $invoices
     * @return array|object
     */
    private function canEditTotal($invoices)
    {
        // TODO delete this code block
        $invoiceDetails = (new Query())
            ->select([
                'invoice_id',
                'item'
            ])
            ->from('invoice_details')
            ->indexBy('invoice_id')
            ->all();

        foreach ($invoices as $key => $invoice) {
            if (!isset($invoiceDetails[$invoice->id])) {
                $invoices[$key]->editTotal = 0;
                continue;
            }
            if (!in_array($invoiceDetails[$invoice->id]['item'], [
                InvoiceDetails::ITEM_PROLONGATION_PANEL,
                InvoiceDetails::ITEM_BUY_CHILD_PANEL,
                InvoiceDetails::ITEM_PROLONGATION_CHILD_PANEL,
            ])) {
                $invoices[$key]->editTotal = 0;
                continue;
            }
            $invoices[$key]->editTotal = 1;
        }

        return $invoices;
    }

    /**
     * Get count panels by type
     * @param int $status
     * @return int
     */
    public function count($status = null)
    {
        $searchQuery = $this->getQuery();

        $query = clone $this->buildQuery($status);

        if (!empty($searchQuery)) {
            $query = $this->addDomainJoinQuery($query);
        }

        return $query->count();
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => Yii::t('app/superadmin', 'invoices.list.navs_all', [
                'count' => $this->count()
            ]),
            Invoices::STATUS_UNPAID => Yii::t('app/superadmin', 'invoices.list.navs_unpaid', [
                'count' => $this->count(Invoices::STATUS_UNPAID)
            ]),
            Invoices::STATUS_PAID => Yii::t('app/superadmin', 'invoices.list.navs_paid', [
                'count' => $this->count(Invoices::STATUS_PAID)
            ]),
            Invoices::STATUS_CANCELED => Yii::t('app/superadmin', 'invoices.list.navs_canceled', [
                'count' => $this->count(Invoices::STATUS_CANCELED)
            ]),
        ];
    }

    /**
     * Get domain
     * @return string
     */
    public function getDomain()
    {
        return $this->domain ? DomainsHelper::idnToUtf8($this->domain) : '';
    }

    /**
     * Get labels of search types
     * @return array
     */
    public function getSearchTypes()
    {
        return [
            static::SEARCH_TYPE_INVOICE_ID => Yii::t('app/superadmin', 'invoices.list.search_type_invoice_id'),
            static::SEARCH_TYPE_DOMAIN => Yii::t('app/superadmin', 'invoices.list.search_type_domain'),
            static::SEARCH_TYPE_CUSTOMER => Yii::t('app/superadmin', 'invoices.list.search_type_customer'),
        ];
    }
}