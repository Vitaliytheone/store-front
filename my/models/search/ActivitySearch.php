<?php

namespace my\models\search;

use my\helpers\ActivityLogHelper;
use Yii;
use common\models\panels\Project;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use DateTime;

/**
 * Class ActivitySearch
 * @package my\models\search
 */
class ActivitySearch extends Model
{
    const DAY_TIME = 86400;

    const DATE_FORMAT = 'Y-m-d';

    const QUERY_TYPE_DETAILS = 1;
    const QUERY_TYPE_IP_ADDRESS = 2;
    const QUERY_TYPE_EVENT_ID = 3;

    public $from;
    public $to;

    /**
     * @var int
     */
    protected $pageSize = 100;

    /**
     * @var array
     */
    private $params;

    /**
     * @var Project
     */
    private $_panel;

    /**
     * @var DateTime
     */
    private $_dateTime;

    /**
     * @var array
     */
    private static $_accounts;

    /**
     * @var array
     */
    private static $_events;

    /**
     * @var array
     */
    private static $_activity;

    /**
     * @var integer
     */
    private static $_interval;

    public function __construct($config = [])
    {
        $this->_dateTime = new DateTime();

        return parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'date', 'format' => 'php:Y-m-d'],
            [['from'], 'compareDateValidator'],
        ];
    }

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
        $this->attributes = $params;
    }

    /**
     * Get search parameters
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'from' => $this->getFrom(false),
            'to' => $this->getTo(false),
            'event' => ArrayHelper::getValue($this->params, 'event'),
            'account' => ArrayHelper::getValue($this->params, 'account'),
            'query_type' => ArrayHelper::getValue($this->params, 'query_type'),
        ];
    }

    /**
     * Get search query
     * @return mixed
     */
    public function getQuery()
    {
        $query = (string)ArrayHelper::getValue($this->params, 'query', '');
        $query = trim($query);
        return !empty($query) ? $query : null;
    }

    /**
     * Get search from
     * @param boolean $isTimestamp
     * @return mixed
     */
    public function getFrom($isTimestamp = true)
    {
        $from = (string)ArrayHelper::getValue($this->params, 'from', '');
        $from = trim($from);

        if (empty($from)) {
            $this->_dateTime->setTimestamp($this->getTo());
            $this->_dateTime->modify('-1 week');
        } else {
            $this->_dateTime = DateTime::createFromFormat(static::DATE_FORMAT, $from);
        }

        $this->_dateTime->setTime(0, 0, 0);

        return $isTimestamp ? $this->_dateTime->getTimestamp() : $this->_dateTime->format('Y-m-d');
    }

    /**
     * Get search to
     * @param boolean $isTimestamp
     * @return mixed
     */
    public function getTo($isTimestamp = true)
    {
        $to = (string)ArrayHelper::getValue($this->params, 'to', '');
        $to = trim($to);

        if (empty($to)) {
            $this->_dateTime->setTimestamp(time());
        } else {
            $this->_dateTime = DateTime::createFromFormat(static::DATE_FORMAT, $to);
        }

        $this->_dateTime->setTime(0, 0, 0);

        return $isTimestamp ? $this->_dateTime->getTimestamp() : $this->_dateTime->format('Y-m-d');
    }

    /**
     * Set panel
     * @param Project $panel
     */
    public function setPanel($panel)
    {
        $this->_panel = $panel;
    }

    /**
     * Build sql query
     * @param null|array $event
     * @param null|array $account
     * @return Query
     */
    /**
     * @param null $event
     * @param null $account
     * @return $this
     */
    public function buildQuery($event = null, $account = null)
    {
        $searchQuery = $this->getQuery();
        $from = $this->getFrom();
        $to = $this->getTo() + static::DAY_TIME;
        $queryType = ArrayHelper::getValue($this->params, 'query_type');

        $query = (new Query())
            ->select([
                'id',
                'admin_id',
                'created_at',
                'ip',
                'details',
                'details_id',
                'event',
            ])
            ->from($this->_panel->db . '.activity_log')
            ->andWhere('super_user <> 1');

        $query->andWhere([
            'between',
            'created_at',
            $from,
            $to
        ]);

        if (!empty($event)) {
            $query->andWhere(['event' => (array)$event]);
        }

        if (!empty($account)) {
            $query->andWhere(['admin_id' => (array)$account]);
        }

        if (!empty($searchQuery)) {
            switch ($queryType) {
                case static::QUERY_TYPE_DETAILS:
                    $query->andWhere(['like', 'activity_log.details', $searchQuery]);
                break;

                case static::QUERY_TYPE_IP_ADDRESS:
                    $query->andWhere(['=', 'activity_log.ip', $searchQuery]);
                break;

                case static::QUERY_TYPE_EVENT_ID:
                    $query->andWhere(['=', 'activity_log.event', $searchQuery]);
                break;

                default:
                    $query->andWhere('1 = 0');
            }
        }

        return $query;
    }

    /**
     * Get timezone
     * @return int - in minutes
     */
    public function getTimezone()
    {
        $timezone = 0;
        if (!Yii::$app->user->isGuest) {
            $timezone = Yii::$app->user->identity->timezone;
        }
        return ((int)$timezone);
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $pages = new Pagination(['totalCount' => $this->count()]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        $event = ArrayHelper::getValue($this->params, 'event');
        $account = ArrayHelper::getValue($this->params, 'account');

        $query = clone $this->buildQuery($event, $account);

        $query->limit($pages->limit)
            ->offset($pages->offset)
            ->orderBy([
                'id' => SORT_DESC
            ]);

        return [
            'models' => $this->prepareData($this->queryAll($query)),
            'pages' => $pages
        ];
    }

    /**
     * Get count activity logs
     * @return int
     */
    public function count()
    {
        $event = ArrayHelper::getValue($this->params, 'event');
        $account = ArrayHelper::getValue($this->params, 'account');

        $query = clone $this->buildQuery($event, $account);

        return $query->select('COUNT(*)')->scalar();
    }

    /**
     * Prepare log data
     * @param array $items
     * @return array
     */
    public function prepareData(array $items)
    {
        $returnItems = [];

        $accounts = $this->getAccounts();

        foreach ($items as $key => $item) {
            $returnItems[$key] = [
                'id' => $item['id'],
                'account' => !empty($accounts[$item['admin_id']]) ? $accounts[$item['admin_id']] : '',
                'date' => Yii::$app->formatter->asDate($item['created_at'] + $this->getTimezone(), 'php:Y-m-d H:i:s'),
                'ip' => $item['ip'],
                'details' => $item['details'],
                'event' => ActivityLogHelper::getEventName($item['event']),
            ];
        }

        return $returnItems;
    }

    /**
     * Get accounts
     * @return array
     */
    public function getAccounts()
    {
        if (null !== static::$_accounts) {
            return static::$_accounts;
        }

        static::$_accounts = [];

        $query = (new Query())
            ->select([
                'id',
                'login'
            ])
            ->from('project_admin')
            ->andWhere([
                'pid' => $this->_panel->id
            ]);

        foreach ($this->queryAll($query) as $account) {
            static::$_accounts[$account['id']] = $account['login'];
        }

        return static::$_accounts;
    }

    /**
     * Get accounts
     * @return array
     */
    public function getEvents()
    {
        if (null !== static::$_events) {
            return static::$_events;
        }

        static::$_events = [];

        $account = ArrayHelper::getValue($this->params, 'account');

        $query = clone $this->buildQuery(null, $account);

        $query->select([
           'event',
            'COUNT(*) as rows'
        ])
        ->groupBy('event');

        $rowsByEvents = ArrayHelper::map($this->queryAll($query), 'event', 'rows');

        foreach (ActivityLogHelper::getEvents() as $event => $eventName) {
            $count = ArrayHelper::getValue($rowsByEvents, $event, 0);
            static::$_events[$event] = $eventName . " ({$count})";
        }

        return static::$_events;
    }

    /**
     * Get events by group
     * @return array
     */
    public function getEventsByGroups()
    {
        $events = $this->getEvents();
        $eventsByGroups = [];

        foreach (Yii::$app->params['activityTypesByGroups'] as $key => $group) {
            $groupEvents = [];

            foreach ($group['events'] as $eventId) {
                if (!empty($events[$eventId])) {
                    $groupEvents[$eventId] = $events[$eventId];
                }
            }

            if (empty($groupEvents)) {
                continue;
            }

            $eventsByGroups[$key] = [
                'title' => $group['title'],
                'events' => $groupEvents
            ];
        }

        return $eventsByGroups;
    }

    /**
     * Get prepared activity data
     * @return array
     */
    public function getActivity()
    {
        if (null !== static::$_activity) {
            return static::$_activity;
        }

        static::$_activity = $activity = [];

        $event = ArrayHelper::getValue($this->params, 'event');
        $account = ArrayHelper::getValue($this->params, 'account');

        $query = clone $this->buildQuery($event, $account);

        $dateFrom = $this->getFrom();
        $dateTo = $this->getTo();

        $query->select([
                "created_at",
                'COUNT(*) as rows'
            ]);

        $type = 'hour';

        $days = (($dateTo - $dateFrom) / static::DAY_TIME) + 1;

        if (in_array($days, [1, 2])) {
            static::$_interval = static::DAY_TIME / 24;
            $dateTo += static::DAY_TIME;

            $query->groupBy("(HOUR(FROM_UNIXTIME(`created_at`)))");

            for ($point = $dateFrom; $point <= $dateTo; $point = $point + static::$_interval) {
                $activity[$point] = [
                    'point' => $point,
                    'count' => 0
                ];
            }
        } else {
            $dateTo += static::DAY_TIME;
            static::$_interval = static::DAY_TIME;

            $this->_dateTime->setTimestamp($dateFrom);

            $fromYear = $this->_dateTime->format('Y');
            $fromMonth = $this->_dateTime->format('m');
            $fromDay = $this->_dateTime->format('d');

            $this->_dateTime->setTimestamp($dateTo);

            $toYear = $this->_dateTime->format('Y');
            $toMonth = $this->_dateTime->format('m');
            $toDay = $this->_dateTime->format('d');
            
            $groupBy = [];

            if ($fromYear != $toYear) {
                // Если разница в 1 год, то проверяем пересечение месяцев
                if ($toYear == ($fromYear + 1)) {
                    if ($toMonth >= $fromMonth) {
                        $groupBy[] = 'YEAR(FROM_UNIXTIME(`created_at`))';
                    }
                } else {
                    $groupBy[] = 'YEAR(FROM_UNIXTIME(`created_at`))';
                }
            }

            if ($fromMonth != $toMonth) {
                // Если разница в 1 год, то проверяем пересечение месяцев
                if ($toMonth == ($fromMonth + 1)) {
                    if ($toDay >= $fromDay) {
                        $groupBy[] = 'MONTH(FROM_UNIXTIME(`created_at`))';
                    }
                } else {
                    $groupBy[] = 'MONTH(FROM_UNIXTIME(`created_at`))';
                }
            }

            $groupBy[] = 'DAY(FROM_UNIXTIME(`created_at`))';

            $query->groupBy($groupBy);

            $type = 'day';

            for ($point = $dateFrom; $point < $dateTo; $point = $point + static::$_interval) {
                $activity[$point] = [
                    'point' => $point,
                    'count' => 0
                ];
            }
        }

        foreach ($this->queryAll($query) as $item) {
            $this->_dateTime->setTimestamp($item['created_at']);

            if ('hour' == $type) {
                $this->_dateTime->setTime($this->_dateTime->format('h'), 0, 0);
            } else {
                $this->_dateTime->setTime(0, 0, 0);
            }

            $date = $this->_dateTime->getTimestamp();
            $activity[$date] = [
                'point' => $date,
                'count' => $item['rows']
            ];
        }

        foreach (array_values($activity) as $data) {
            static::$_activity[] = [
                'point' => $data['point'] + $this->getTimezone(),
                'count' => $data['count']
            ];
        }

        return static::$_activity;
    }

    /**
     * Get interval
     * @return int
     */
    public function getInterval()
    {
        if (null === static::$_interval) {
            $this->getActivity();
        }

        return static::$_interval;
    }

    /**
     * Get query types
     * @return array
     */
    public function getQueryTypes()
    {
        return [
            static::QUERY_TYPE_DETAILS => 'Details',
            static::QUERY_TYPE_IP_ADDRESS => 'IP address',
            static::QUERY_TYPE_EVENT_ID => 'Event ID',
        ];
    }

    /**
     * Compare date validator
     * @param string $attribute
     * @param array $params
     */
    public function compareDateValidator($attribute, $params = [])
    {
        if ($this->hasErrors()) {
            return false;
        }

        if ($this->getFrom() > $this->getTo()) {
            $this->addError($attribute, 'From value can not be greater than the value To.');
            return false;
        }
    }

    /**
     * Query all and use cache 60 seconds
     * @param Query $query
     * @return array
     */
    public function queryAll(Query $query)
    {
        if (time() < $this->getTo()) {
            return $query->all();
        }

        return $query->createCommand()->cache(60)->queryAll();
    }
}