<?php
namespace my\modules\superadmin\models\search;

use Yii;
use common\models\panels\Tickets;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class TicketsSearch
 * @package my\modules\superadmin\models\search
 */
class TicketsSearch extends Tickets
{
    use SearchTrait;

    protected $pageSize = 500;

    public $rows;
    public $customer_email;

    /**
     * Cached counts tickets by status
     * @var array
     */
    private $_counts_by_status = [];

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all',
        ];
    }

    /**
     * Build main search query
     * @param int $status
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();

        $query = static::find();

        if (!empty($searchQuery)) {
            $customerId = null;

            if (-1 !== strpos('@', $searchQuery)) {
                    $customerId = (new Query())
                    ->select('id')
                    ->from('customers')
                    ->andWhere([
                        'email' => $searchQuery
                    ])
                    ->scalar();
            }

            if ($customerId) {
                $query->andWhere([
                    'tickets.cid' => $customerId
                ]);
            } else {
                $ticketsIds = (new Query())
                    ->select(['tid'])
                    ->from('ticket_messages')
                    ->andWhere([
                        'or',
                        ['like', 'ticket_messages.message', $searchQuery],
                    ])
                    ->groupBy('tid')
                    ->column();

                $query->groupBy('tickets.id');

                $query->andFilterWhere([
                    'or',
                    ['=', 'tickets.id', $searchQuery],
                    ['like', 'tickets.subject', $searchQuery],
                    ['in', 'tickets.id', $ticketsIds],
                ]);
            }
        }

        if ('all' !== $status && null !== $status) {
            $query->andWhere([
                'tickets.status' => $status
            ]);
        }

        $query->select([
            'tickets.id',
            'tickets.user',
            'tickets.subject',
            'tickets.status',
            'tickets.date',
            'tickets.date_update',
            'tickets.cid',
            'customers.email AS customer_email'
        ]);
        $query->leftJoin('customers', 'tickets.cid = customers.id');

        return $query;
    }

    /**
     * Return cached counted tickets for statuses
     * considering search query string
     * @return array
     */
    public function setCountsByStatus()
    {
        $query = clone $this->buildQuery(null);

        $this->_counts_by_status = $query
            ->select(['count' => 'COUNT(DISTINCT tickets.id)', 'status' => 'tickets.status'])
            ->groupBy('tickets.status')
            ->indexBy('status')
            ->column();

        return $this->_counts_by_status;
    }

    /**
     * Return topics count for status or all
     * @param null $status
     * @return float|int|mixed
     */
    public function getCountByStatus($status = null)
    {
        if ($status === null || $status === 'all') {
            return array_sum($this->_counts_by_status);
        }

        return ArrayHelper::getValue($this->_counts_by_status, $status);
    }

    /**
     * Return topics counts for each status
     * @return array
     */
    public function getCountsByStatuses()
    {
        return $this->_counts_by_status;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $this->setCountsByStatus();

        $status = isset($this->params['status']) ? $this->params['status'] : 'all';

        $query = clone $this->buildQuery($status);

        $pages = new Pagination(['totalCount' => $this->getCountByStatus($status)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $tickets = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'date_update' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $tickets,
            'pages' => $pages,
        ];
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        $countsByStatus = $this->getCountsByStatuses();

        $navs = [
            'all' => Yii::t('app/superadmin', 'tickets.list.navs_all', [
                'count' => array_sum($countsByStatus)
            ]),
            Tickets::STATUS_PENDING => Yii::t('app/superadmin', 'tickets.list.navs_pending', [
                'count' => ArrayHelper::getValue($countsByStatus, Tickets::STATUS_PENDING, 0)
            ]),
            Tickets::STATUS_RESPONDED => Yii::t('app/superadmin', 'tickets.list.navs_responded', [
                'count' => ArrayHelper::getValue($countsByStatus, Tickets::STATUS_RESPONDED, 0)
            ]),
            Tickets::STATUS_SOLVED => Yii::t('app/superadmin', 'tickets.list.navs_solved', [
                'count' => ArrayHelper::getValue($countsByStatus, Tickets::STATUS_SOLVED, 0)
            ]),
            Tickets::STATUS_IN_PROGRESS => Yii::t('app/superadmin', 'tickets.list.navs_in_progress', [
                'count' => ArrayHelper::getValue($countsByStatus, Tickets::STATUS_IN_PROGRESS, 0)
            ]),
            Tickets::STATUS_CLOSED => Yii::t('app/superadmin', 'tickets.list.navs_closed', [
                'count' => ArrayHelper::getValue($countsByStatus, Tickets::STATUS_CLOSED, 0)
            ]),
        ];

        return $navs;
    }
}