<?php

namespace control_panel\components\scanners\components\scanners;

use control_panel\components\scanners\components\BaseScanner;
use common\models\panels\SuperToolsScanner;
use yii\base\Exception;

/**
 * Class LevopanelScanner
 * @package control_panel\components\scanners\components\scanners
 */
class LevopanelScanner extends BaseScanner
{
    protected static $pageSize = 100;

    public $panelInfoClass = 'control_panel\components\scanners\components\info\LevopanelInfo';

    public $nameserversList = [
        'clyde.ns.cloudflare.com',
        'demi.ns.cloudflare.com',
    ];

    public static $scannerName = 'levopanel';

    public static $panel = SuperToolsScanner::PANEL_LEVOPANEL;

    /**
     * @return array
     * @throws Exception
     */
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

        // Take only those domains that we do not have in DB
        return $this->getNewDomains($intersectedDomains);
    }

}