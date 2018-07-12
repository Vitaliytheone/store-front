<?php

namespace my\components\scanners\components\scanners;

use my\components\scanners\components\BaseScanner;
use common\models\panels\SuperToolsScanner;
use yii\base\Exception;
use my\components\scanners\components\info\RentapanelInfo;

/**
 * Class RentalpanelScanner
 * @package my\components\scanners\components\scanners
 */
class RentalpanelScanner extends BaseScanner
{
    protected static $pageSize = 100;

    public $panelInfoClass = RentapanelInfo::class;

    public $nameserversList = [
        'chris.ns.cloudflare.com',
        'tani.ns.cloudflare.com'
    ];

    public static $scannerName = 'rentalpanel';

    public static $panel = SuperToolsScanner::PANEL_RENTALPANEL;

    /** @inheritdoc */
    protected function fetchDomains()
    {
        if (count($this->nameserversList) < 2) {
            throw new Exception('Invalid sources number! Must be 2 or more!');
        }

        $nameserversDomains = [];

        // Scan each nameserver for hosted domains
        foreach ($this->nameserversList as $nameserver) {
            $nameserversDomains[] = $this->searchNSNeighbors($nameserver);
        }

        // Intersect 2 domains lists
        $intersectedDomains = call_user_func_array('array_intersect', $nameserversDomains);
        $intersectedDomains = array_reverse($intersectedDomains);

        // Take only those domains that we do not have in DB
        return $this->getNewDomains($intersectedDomains);
    }
}


