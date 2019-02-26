<?php

namespace superadmin\models\search;

use common\models\panels\CustomersNote;
use common\models\panels\Domains;
use common\models\panels\SslCert;
use common\models\stores\Stores;

/**
 * Search info by customer
 * Class TicketBlockSearch
 * @package superadmin\models\search
 */
class TicketBlocksSearch
{
    /**
     * @param int $customerId
     * @return array
     */
    public static function search(int $customerId): array 
    {
        return [
            'domains' => self::_getDomains($customerId),
            'ssl' => self::_getSSl($customerId),
            'stores' => self::_getStores($customerId),
            'notes' => self::_getNotes($customerId),
        ];
    }

    /**
     * @param int $customerId
     * @return array|SslCert[]
     */
    private static function _getSSL(int $customerId)
    {
        $query = SslCert::find();

        $query->where([
            '=',
            'cid', $customerId,
        ]);
        $query->andWhere(['status' => SslCert::STATUS_ACTIVE]);

        $query->select([
            'id',
            'status AS status',
            'domain AS domain'
        ]);

        $query->orderBy("id DESC");

        return $query->all();
    }

    /**
     * @param int $customerId
     * @return array|Stores[]
     */
    private static function _getStores(int $customerId)
    {
        $query = Stores::find();

        $query->where([
            '=',
            'customer_id', $customerId,
        ]);

        $query->select([
            'status AS status',
            'domain AS domain',
            'id'
        ]);

        $query->orderBy("id DESC");

        return $query->all();
    }

    /**
     * @param int $customerId
     * @return array|Domains[]
     */
    private static function _getDomains(int $customerId)
    {
        $query = Domains::find();

        $query->where([
            '=',
            'customer_id', $customerId,
        ]);

        $query->select([
            'id',
            'status AS status',
            'domain AS domain'
        ]);

        $query->orderBy("id DESC");

        return $query->all();
    }

    /**
     * @param int $customerId
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function _getNotes(int $customerId)
    {
        $query = CustomersNote::find();

        $query->where([
            '=',
            'customer_id', $customerId,
        ]);

        $query->select([
            'id',
            'note',
            'customer_id',
        ]);

        $query->orderBy("id DESC");

        return $query->all();
    }
}
