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
        'chris.ns.cloudflare.com'
    ];

    public static $scannerName = 'rentalpanel';

    public static $panel = SuperToolsScanner::PANEL_RENTALPANEL;

    /** @inheritdoc */
    protected function fetchDomains()
    {

        if (count($this->nameserversList) < 1) {
            throw new Exception('Invalid sources number! Must be 1 or more!');
        }

        if (count($this->nameserversList) > 1) {

            $nameserversDomains = [];

            // Scan each nameserver for hosted domains
            foreach ($this->nameserversList as $nameserver) {
                $nameserversDomains[] = $this->searchNSNeighbors($nameserver);
            }

            // Intersect 2 domains lists
            $domains = call_user_func_array('array_intersect', $nameserversDomains);

        }  else {
            $domains = $this->searchNSNeighbors($this->nameserversList[0]);
        }

        // Take only those domains that we do not have in DB
        $newDomains = $this->getNewDomains($domains);

        shuffle($newDomains);

        return $newDomains;
    }
}
