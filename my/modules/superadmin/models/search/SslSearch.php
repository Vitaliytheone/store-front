<?php
namespace superadmin\models\search;

use Yii;
use common\models\panels\SslCert;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SslSearch
 * @package superadmin\models\search
 */
class SslSearch extends SslCert {

    public $email;

    protected $pageSize = 100;

    public $rows;

    protected static $_sslCerts;

    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery()
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
        $id = ArrayHelper::getValue($this->params, 'id');
        $customerId = ArrayHelper::getValue($this->params, 'customer_id');

        $sslList = static::find();

        $sslList->select([
            'ssl_cert.*',
            'customers.email as email'
        ]);
        $sslList->leftJoin('customers', 'customers.id = ssl_cert.cid');


        if (null !== $status && '' !== $status) {
            $sslList->andWhere([
                'ssl_cert.status' => $status
            ]);
        }


        if (!empty($searchQuery)) {
            $sslList->andFilterWhere([
                'or',
                ['=', 'ssl_cert.id', $searchQuery],
                ['like', 'ssl_cert.domain', $searchQuery],
            ]);
        }

        if ($customerId) {
            $sslList->andWhere([
                'ssl_cert.cid' => $customerId
            ]);
        }

        if ($id) {
            $sslList->andWhere([
                'ssl_cert.id' => $id
            ]);
        }

        return $sslList;
    }

    /**
     * Search ssl
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status');

        $query = clone $this->buildQuery($status);

        $pages = new Pagination(['totalCount' => $this->count($status)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $ssl = $query
            ->offset($pages->offset)
            ->limit($pages->limit)->orderBy([
                'ssl_cert.id' => SORT_DESC
            ])->groupBy('ssl_cert.id');

        return [
            'models' => static::queryAllCache($ssl),
            'pages' => $pages,
        ];
    }

    /**
     * Get count ssl by type
     * @param int $status
     * @param array $filters
     * @return int|array
     */
    public function count($status = null, $filters = [])
    {
        $query = clone $this->buildQuery($status);

        if (!empty($filters['group']['status'])) {
            $query->select([
                'ssl_cert.status as status',
                'COUNT(DISTINCT ssl_cert.id) as rows'
            ])->groupBy('ssl_cert.status');

            return ArrayHelper::map(static::queryAllCache($query), 'status', 'rows');
        }

        $query->select('COUNT(DISTINCT ssl_cert.id)');

        return (int)static::queryScalarCache($query);
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        $statusCounters = $this->count(null, [
            'group' => [
                'status' => 1
            ],
        ]);

        return [
            null => Yii::t('app/superadmin', 'ssl.list.navs_all', [
                'count' => $this->count()
            ]),
            SslCert::STATUS_PENDING => Yii::t('app/superadmin', 'ssl.list.navs_pending', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_PENDING, 0)
            ]),
            SslCert::STATUS_ACTIVE => Yii::t('app/superadmin', 'ssl.list.navs_active', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_ACTIVE, 0)
            ]),
            SslCert::STATUS_PROCESSING => Yii::t('app/superadmin', 'ssl.list.navs_processing', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_PROCESSING, 0)
            ]),
            SslCert::STATUS_PAYMENT_NEEDED => Yii::t('app/superadmin', 'ssl.list.navs_payment_needed', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_PAYMENT_NEEDED, 0)
            ]),
            SslCert::STATUS_CANCELED => Yii::t('app/superadmin', 'ssl.list.navs_canceled', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_CANCELED, 0)
            ]),
            SslCert::STATUS_INCOMPLETE => Yii::t('app/superadmin', 'ssl.list.navs_incomplete', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_INCOMPLETE, 0)
            ]),
            SslCert::STATUS_EXPIRED => Yii::t('app/superadmin', 'ssl.list.navs_expired', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_EXPIRED, 0)
            ]),
            SslCert::STATUS_ERROR => Yii::t('app/superadmin', 'ssl.list.navs_ddos_error', [
                'count' => ArrayHelper::getValue($statusCounters, SslCert::STATUS_ERROR, 0)
            ]),
        ];
    }
}