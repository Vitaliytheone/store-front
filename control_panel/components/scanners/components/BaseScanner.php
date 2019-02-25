<?php

namespace control_panel\components\scanners\components;

use control_panel\components\scanners\components\Tcpiputils;
use control_panel\components\scanners\components\BasePanelInfo;
use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\SuperToolsScanner;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class LevopanelComponent
 * @package control_panel\components\levopanel
 */
abstract class BaseScanner extends Component
{
    use UnixTimeFormatTrait;

    /**
     * Requests page size
     * @var int
     */
    protected static $pageSize = 100;

    /**
     * Scanner name
     * @var
     */
    public static $scannerName;

    /**
     * Panel type:
     * 1 - Levopanel, 2 - Smmfire
     * @var string;
     */
    public static $panel;

    /**
     * Panel info class name
     * @var string
     */
    public $panelInfoClass;

    /**
     * Panel info requests proxy data
     * @var array
     */
    public $proxy = [];

    /**
     * Panel info requests timeouts
     * @var array
     */
    public $timeouts = [];

    /**
     * Api Key
     * https://www.tcpiputils.com/api
     * @var string
     */
    public $apiKey;

    /**
     * List of the NS for
     * research on domains sharing the same name servers
     * @var array
     */
    public $nameserversList = [];

    /**
     * Statuses of verified panels that can be added
     * @var array
     */
    public $panelStatuses = [
        SuperToolsScanner::PANEL_STATUS_ACTIVE,
        SuperToolsScanner::PANEL_STATUS_DISABLED,
//        SuperToolsScanner::PANEL_STATUS_PERFECTPANEL,
    ];

    /**
     * Return table name of used Scanner data storage model
     * @return string
     */
    public function getTable()
    {
       return SuperToolsScanner::tableName();
    }

    /**
     * Returns all domains list sharing the same name server
     * @param string $nameserver
     * @return array
     * @throws Exception
     */
    public function searchNSNeighbors($nameserver)
    {
        echo (PHP_EOL . "NSNeighbors for $nameserver scan started" . PHP_EOL);

        $tcpIpUtils = new Tcpiputils([
            'apiKey' => $this->apiKey,
        ]);

        $domains = [];
        $pagesLimit = 10;
        $page = 1;

        // Get all domain pages for $nameserver
        do {
            $response = $tcpIpUtils->getNSNeighbors($nameserver, $page);
            $pagesTotal = $response['pagesTotal'];

            $domains = array_merge($domains, $response['domains']);

            echo ("Page $page of $pagesTotal completed" . PHP_EOL);

            $page++;
        } while ($page <= $pagesTotal && $page <= $pagesLimit);
        echo ("___________________" . PHP_EOL);
        echo ("Total " . count($domains) . " domains was fetched" . PHP_EOL);
        echo ("Total " . ($page - 1) . " requests was done." . PHP_EOL);
        echo ("NSNeighbors scan completed" . PHP_EOL . PHP_EOL);

        return $domains;
    }

    /**
     * Return exiting panel domains list from db
     * @return array
     */
    public function getDomains()
    {
            $query = (new Query())
            ->select(['id', 'domain'])
                ->from($this->getTable())
                ->andWhere([
                    'panel' => static::$panel
                ]);

        return $query->all();
    }

    /**
     * Return new domains
     * by diffs exiting db domains list and passed captured domains list
     * @param array $domains
     * @return array
     */
    public function getNewDomains(array $domains)
    {
        echo (PHP_EOL . "GetNewDomains task started" . PHP_EOL);
        echo ( "Domains count " . count($domains) . PHP_EOL);

        if (!$domains) {
            return [];
        }

        $exitingDomains = (new Query())
            ->select('domain')
            ->from($this->getTable())
            ->andWhere(['panel' => static::$panel])
            ->column();

        $domains = array_map( 'trim', $domains);

        $newDomains = array_diff($domains, $exitingDomains);

        echo ("New domains count ". count($newDomains) . PHP_EOL);
        echo ("GetNewDomains task completed" . PHP_EOL . PHP_EOL);

        return $newDomains;
    }

    /**
     * Fetch domains list for research sources
     * @return array
     */
    abstract protected function fetchDomains();

    /**
     * Fetch and research _new_ domains
     * Если домен уже в базе: Если статус не панели — метим удаленным, иначе — обновляем статус.
     * Если в базе нет, и статус панели — создаем новый. Без статуса панели - пропускаем.
     */
    public function researchNewDomains()
    {
        $newDomains = $this->fetchDomains();

        if (!$newDomains) {
            echo (PHP_EOL . PHP_EOL . "No new domains for research! Exiting..." . PHP_EOL);
            exit;
        }

        /** @var BasePanelInfo $panelInfoComponent */
        $panelInfoComponent = new $this->panelInfoClass([
            'proxy' => $this->proxy,
            'timeouts' => $this->timeouts,
            'rules' => [
                BasePanelInfo::RULE_STATUS_ACTIVE => true,
                BasePanelInfo::RULE_STATUS_DISABLED => true,
                BasePanelInfo::RULE_STATUS_MOVED => true,
                BasePanelInfo::RULE_STATUS_PERFECTPANEL => false,
            ],
        ]);

        // Check panels
        echo (PHP_EOL . "Task get panel status started" . PHP_EOL);
        echo ( "Domains for checking count: " . count($newDomains) . PHP_EOL);
        echo ( "Domains per request page: " . static::$pageSize . PHP_EOL);

        $taskTime = time();
        $domainsAddedCount = 0;
        $domainsProcessedCount = 0;

        // Get panels info for panels limited by PAGE_SIZE
        while (count($newDomains)) {
            $domains = array_splice($newDomains, 0, static::$pageSize);

            echo '1';
            $panelsInfo = $panelInfoComponent->getPanelsInfo($domains);
            echo '2';

            foreach ($panelsInfo as $panelInfo) {

                $info = $panelInfo['info'];
                $status = $panelInfo['status'];
                $domain = $panelInfo['host'];

                echo 'Checking: ' . $domain;

                if (in_array($status, $this->panelStatuses)) {

                    echo ' ---> Added ';

                    $domainModel = new SuperToolsScanner();
                    $domainModel->setAttributes([
                        'panel_id' => SuperToolsScanner::getNextPanelId(static::$panel),
                        'panel' => static::$panel,
                        'domain' => $domain,
                        'server_ip' => ArrayHelper::getValue($info, 'primary_ip'),
                        'status' => $status,
                        'details' => json_encode($info),
                    ]);

                    $domainModel->save();

                    $domainsAddedCount++;
                } else {
                    echo ' ---> Skipped ';
                }

                echo '[' . SuperToolsScanner::statusesLabels()[$status] . ']' .  PHP_EOL;

                $domainsProcessedCount++;
            }

            echo PHP_EOL . 'Domains left ' . count($newDomains) . PHP_EOL;
        };

        $taskTime = time() - $taskTime;
        echo ( "------------------------" . PHP_EOL);
        echo ( "Estimated task time, seconds [" . $taskTime . "]" . PHP_EOL);
        echo ( "Total domains added: [ " . $domainsAddedCount . " from " . $domainsProcessedCount . " checked" . " ]" . PHP_EOL);
        exit;
    }


    /**
     * Research and update statuses for _all_ panels from db
     */
    public function researchAllDomains()
    {
        $exitingDomains = $this->getDomains();

        /** @var BasePanelInfo $panelInfo */
        $panelInfo = new $this->panelInfoClass([
            'proxy' => $this->proxy,
            'timeouts' => $this->timeouts,
            'rules' => [
                BasePanelInfo::RULE_STATUS_ACTIVE => true,
                BasePanelInfo::RULE_STATUS_DISABLED => true,
                BasePanelInfo::RULE_STATUS_MOVED => true,
                BasePanelInfo::RULE_STATUS_PERFECTPANEL => true,
            ],
        ]);

        foreach ($exitingDomains as $domain) {

            $panelData = $panelInfo->getPanelInfo($domain['domain']);

            $panelStatus = $panelData['status'];
            $panelDomainInfo = $panelData['info'];

            $domainModel = SuperToolsScanner::findOne($domain['id']);

            $domainModel->setAttributes([
                'server_ip' => ArrayHelper::getValue($panelDomainInfo, 'primary_ip'),
                'status' => $panelStatus,
                'details' => json_encode($panelDomainInfo),
            ]);

            $domainModel->save(false);
        }
    }
}