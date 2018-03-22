<?php
namespace my\modules\superadmin\models\search;

use Yii;
use common\models\panels\SslCert;
use yii\helpers\ArrayHelper;

/**
 * Class SslSearch
 * @package my\modules\superadmin\models\search
 */
class SslSearch extends SslCert {

    public $email;

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
     * @return $this
     */
    public function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();
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

        $sslList->orderBy([
            'ssl_cert.id' => SORT_DESC
        ])->groupBy('ssl_cert.id');

        return $sslList;
    }

    /**
     * Get ssl certs
     * @param null|integer $status
     */
    protected function getSslCerts($status = null)
    {
        if (empty(static::$_sslCerts)) {
            static::$_sslCerts = $this->buildQuery()->all();
        }

        if (null === $status || '' === $status) {
            return static::$_sslCerts;
        }

        $sslCerts = [];

        foreach (static::$_sslCerts as $sslCert) {
            if ($sslCert->status != $status) {
                continue;
            }

            $sslCerts[] = $sslCert;
        }

        return $sslCerts;
    }

    /**
     * Search domains
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status');

        return [
            'models' => $this->getSslCerts($status)
        ];
    }

    /**
     * Get count panels by type
     * @param int $status
     * @return int
     */
    public function count($status = null)
    {
        $query = clone $this->buildQuery($status);

        return $query->count();
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => Yii::t('app/superadmin', 'ssl.list.navs_all', [
                'count' => count($this->getSslCerts())
            ]),
            SslCert::STATUS_PENDING => Yii::t('app/superadmin', 'ssl.list.navs_pending', [
                'count' => count($this->getSslCerts(SslCert::STATUS_PENDING))
            ]),
            SslCert::STATUS_ACTIVE => Yii::t('app/superadmin', 'ssl.list.navs_active', [
                'count' => count($this->getSslCerts(SslCert::STATUS_ACTIVE))
            ]),
            SslCert::STATUS_PROCESSING => Yii::t('app/superadmin', 'ssl.list.navs_processing', [
                'count' => count($this->getSslCerts(SslCert::STATUS_PROCESSING))
            ]),
            SslCert::STATUS_PAYMENT_NEEDED => Yii::t('app/superadmin', 'ssl.list.navs_payment_needed', [
                'count' => count($this->getSslCerts(SslCert::STATUS_PAYMENT_NEEDED))
            ]),
            SslCert::STATUS_CANCELED => Yii::t('app/superadmin', 'ssl.list.navs_canceled', [
                'count' => count($this->getSslCerts(SslCert::STATUS_CANCELED))
            ]),
            SslCert::STATUS_INCOMPLETE => Yii::t('app/superadmin', 'ssl.list.navs_incomplete', [
                'count' => count($this->getSslCerts(SslCert::STATUS_INCOMPLETE))
            ]),
            SslCert::STATUS_EXPIRED => Yii::t('app/superadmin', 'ssl.list.navs_expired', [
                'count' => count($this->getSslCerts(SslCert::STATUS_EXPIRED))
            ]),
            SslCert::STATUS_DDOS_ERROR => Yii::t('app/superadmin', 'ssl.list.navs_ddos_error', [
                'count' => count($this->getSslCerts(SslCert::STATUS_DDOS_ERROR))
            ]),
        ];
    }
}