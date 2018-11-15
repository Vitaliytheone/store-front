<?php
namespace superadmin\models\search;

use Yii;
use common\models\panels\Tickets;
use common\models\panels\SuperAdmin;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class TicketsSearch
 * @package superadmin\models\search
 */
class TicketsSearch extends Tickets
{
    use SearchTrait;

    protected $pageSize = 100;
    public $rows;
    public $customer_email;

    /**
     * Cached counts tickets by status
     * @var array
     */
    private $_counts_by_status = null;

    /**
     * Cached superadmins admin
     * @var array
     */
    private $_superadmins = null;


    /**
     * Cached counts tickets by assigned admin
     * @var array
     */
    private $_counts_by_assignee = [];

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all',
            'assignee' => isset($this->params['assignee']) ? $this->params['assignee'] : 'all'
        ];
    }

    /**
     * Build main search query
     * @param int $status
     * @param int $assignee
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery($status = null, $assignee = null)
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
                    'tickets.customer_id' => $customerId
                ]);
            } else {
                $ticketsIds = (new Query())
                    ->select(['ticket_id'])
                    ->from('ticket_messages')
                    ->andWhere(['is_system' => 0])
                    ->andWhere([
                        'or',
                        ['like', 'ticket_messages.message', $searchQuery],
                    ])
                    ->groupBy('ticket_id')
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

        if ('all' !== $assignee && null !== $assignee) {
            $query->andWhere([
                'tickets.assigned_admin_id' => $assignee
            ]);
        }


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
        if ($this->_counts_by_status === null) {
            $this->setCountsByStatus();
        }
        if ($status === null || $status === 'all') {
            return array_sum($this->_counts_by_status);
        }

        return ArrayHelper::getValue($this->_counts_by_status, $status);
    }


    /**
     * Get super admins
     * @return array|SuperAdmin[]
     */
    public function getSuperAdmins()
    {
        if ($this->_superadmins == null) {
            $this->setSuperadmins();
        }
        return $this->_superadmins;
    }

    /**
     * set super admins
     */
    public function setSuperadmins()
    {
        $this->_superadmins = SuperAdmin::find()
            ->indexBy('id')
            ->asArray()
            ->all();
    }

    /**
     * Prepare the data
     * Set assigned name
     * @param $data
     * @return mixed
     */
    private function prepareData($data)
    {
        $superadmins = $this->getSuperAdmins();

        foreach ($data as $key => $ticket) {
            $data[$key]['assigned_name'] = ArrayHelper::getValue($superadmins, [$ticket['assigned_admin_id'], 'username']);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getCountsByAssignee()
    {
        if ($this->_counts_by_assignee == null) {
            $status = isset($this->params['status']) ? $this->params['status'] : 'all';
            $query = clone $this->buildQuery($status);
            $query = $query->select([
                'count' => 'COUNT(DISTINCT tickets.id)',
                'assigned_admin_id' => 'assigned_admin_id'
            ]);
            $query->groupBy('assigned_admin_id');
            $query->indexBy('assigned_admin_id');
            $query->asArray();
            $stat = $query->all();
            $admins = $this->getSuperAdmins();
            $sum = 0;
            foreach ($stat as $key =>  $admin) {
                $sum += $admin['count'];
            }
            foreach ($admins as $key => $admin) {
                if (!isset($stat[$key])) {
                    $stat[$key] = [
                        'count' => 0,
                        'assigned_admin_id' => $key,
                        'username' => $admin['username']
                    ];
                } else {
                    $stat[$key]['username'] = $admins[$key]['username'];
                }
            }

            $this->_counts_by_assignee = $stat;
        }

        return $this->_counts_by_assignee;
    }

    /**
     * @return int|null
     */
    public function getSuperadminsCount()
    {
        $count = null;

        if ($this->_counts_by_assignee) {
            $count = 0;
            foreach ($this->_counts_by_assignee as $admin) {
                $count += $admin['count'];
            }
        }

        return $count;
    }

    /**
     * Return topics counts for each status
     * @return array
     */
    public function getCountsByStatuses()
    {
        if ($this->_counts_by_status === null) {
            $this->setCountsByStatus();
        }
        return $this->_counts_by_status;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = isset($this->params['status']) ? $this->params['status'] : 'all';
        $assignee = isset($this->params['assignee']) ? $this->params['assignee'] : 'all';

        $query = clone $this->buildQuery($status, $assignee);

        $query->select([
            'tickets.id',
            'tickets.is_user',
            'tickets.subject',
            'tickets.status',
            'tickets.assigned_admin_id',
            'tickets.created_at',
            'tickets.updated_at',
            'tickets.customer_id',
            'customers.email AS customer_email'
        ]);
        $query->leftJoin('customers', 'tickets.customer_id = customers.id');

        $queryCount = clone $query;
        $pages = new Pagination(['totalCount' => $queryCount->count()]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $tickets = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'updated_at' => SORT_DESC
            ])
            ->all();

        return [
            'models' => $this->prepareData($tickets),
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