<?php

namespace my\modules\superadmin\models\search;

use common\models\panels\Project;
use common\models\panels\Domains;
use common\models\panels\SslCert;
use common\models\panels\TicketNotes;
use common\models\stores\Stores;

/**
 * Search info by customer
 * Class TicketBlockSearch
 * @package my\modules\superadmin\models\search
 */
class TicketBlocksSearch
{
    /**
     * @param $customerId
     * @return array
     */
    public static function search($customerId)
    {
        return [
            'panels' => self::_getPanels($customerId),
            'childPanels' => self::_getChildPanels($customerId),
            'domains' => self::_getDomains($customerId),
            'ssl' => self::_getSSl($customerId),
            'stores' => self::_getStores($customerId),
            'notes' => self::_getNotes($customerId),
        ];
    }

    /**
     * @param $customerId
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function _getPanels($customerId)
    {
        $query = Project::find();
        $query->where([
            '=',
            'cid', $customerId,
        ]);
        $query->andWhere([
            '=',
            'child_panel',
            0
        ]);
        $query->select([
            'id',
            'act AS act',
            'site AS site'
        ]);
        $query->orderBy("id DESC");

        return $query->all();
    }

    /**
     * @param $customerId
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function _getChildPanels($customerId)
    {
        $query = Project::find();
        $query->where([
            '=',
            'cid', $customerId,
        ]);
        $query->andWhere([
            '=',
            'child_panel',
            1
        ]);
        $query->select([
            'id',
            'act AS act',
            'site AS site'
        ]);

        $query->orderBy("id DESC");

        return $query->all();
    }

    /**
     * @param $customerId
     * @return array|SslCert[]
     */
    private static function _getSSL($customerId)
    {
        $query = SslCert::find();

        $query->where([
            '=',
            'cid', $customerId,
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
     * @param $customerId
     * @return array|Stores[]
     */
    private static function _getStores($customerId)
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
     * @param $customerId
     * @return array|Domains[]
     */
    private static function _getDomains($customerId)
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
     * @param $customerId
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function _getNotes($customerId) {
        $query = TicketNotes::find();

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