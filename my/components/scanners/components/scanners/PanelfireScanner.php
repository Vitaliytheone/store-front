<?php
namespace my\components\scanners\components\scanners;

use my\components\scanners\components\BaseScanner;
use common\models\panels\SuperToolsScanner;
use yii\base\Exception;

/**
 * Class PanelfireScanner
 * @package my\components\scanners\components\scanners
 */
class PanelfireScanner extends BaseScanner
{
    protected static $pageSize = 200;

    public $panelInfoClass = 'my\components\scanners\components\info\PanelfireInfo';

    public $nameserversList = [
        'drew.ns.cloudflare.com',
//        'nina.ns.cloudflare.com',
    ];

    public static $scannerName = 'smmfire';

    public static $panel = SuperToolsScanner::PANEL_PANELFIRE;

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