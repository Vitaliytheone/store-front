<?php
namespace common\components\panelchecker;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;

/**
 * Class PanelcheckerComponent
 * @property string table
 * @package common\components\panelchecker
 */
class PanelcheckerComponent extends Component
{

    public $db = [
        'name' => 'store',
        'table' => 'test',
    ];
    
    public $dbFields = [
        'domains_column' => 'domain',
        'status_column' => 'status',
        'updated_column' => 'updated_at',
        'created_column' => 'created_at',
        'ip_column' => 'server_ip',
        'details_column' => 'details',
    ];
 
    public $panelHostName = 'levopanel.com';
    public $panelIp = '147.135.223.128';

    public $apiKey;
    public $apiVersion = '1.0';

    const API_URL = 'https://www.utlsapi.com/api.php';
    const API_METHOD_DOMAIN_NEIGHBORS = 'domainneighbors';
    const API_RESPONSE_STATUS_ERROR = 'error';

    const PERFECTPANEL_IP = '137.74.23.77';

    // Panel statuses
    const PANEL_STATUS_ACTIVE           = 1;
    const PANEL_STATUS_FROZEN           = 2;
    const PANEL_STATUS_PERFECTPANEL     = 3;
    const PANEL_STATUS_NOT_RESOLVED     = 4;
    const PANEL_STATUS_IP_NOT_LEVOPANEL = 5;
    const PANEL_STATUS_PARKING          = 6;
    const PANEL_STATUS_OTHER            = 7;

    /**
     * @var array
     */
    private static $_inactivePanelSigns = [
        'Panel is disabled',
    ];

    private static $_activePanelSigns = [
        'name="username"',
        'name="password"',
    ];

    private static $_parkingPageSign = [
        'match_counts' => [
            'needle' => 'godaddy.com',
            'counts' => 15
        ]
    ];

    /**
     * Return DB table
     * @return string
     */
    public function getTable()
    {
        return $this->db['name'] . '.' . $this->db['table'];
    }



    /**
     * Return panel neighbors domains list from API
     * @param string $hostName
     * @return array
     * @throws Exception
     */
    public function searchPanelNeighbors($hostName)
    {
        $requestParams = [
            'type' => self::API_METHOD_DOMAIN_NEIGHBORS,
            'q' => $hostName,
        ];

        $response = $this->request($requestParams);

        $error = self::API_RESPONSE_STATUS_ERROR === strtolower(ArrayHelper::getValue($response,'status'));

        if ($error) {
            throw new Exception("Tcpiputils.com API response error! " . json_encode($response));
        }

        return ArrayHelper::getValue($response,'data.domains', []);
    }


    /**
     * Return exiting panel neighbors domains list
     * @return array
     */
    public function getPanelNeighbors()
    {
        $exitingDomains = (new Query())
            ->select([
                $this->dbFields['domains_column'], $this->dbFields['status_column'], $this->dbFields['updated_column'], $this->dbFields['created_column']
            ])
            ->from($this->table)
            ->all();

        return $exitingDomains;
    }

    /**
     * Insert only new domains list to domains table
     * @param array $domains
     * @return int
     */
    public function updatePanelDomainsList(array $domains)
    {
        $exitingDomains = (new Query())
            ->select($this->dbFields['domains_column'])
            ->from($this->table)
            ->column();

        $newDomains = array_diff($domains, $exitingDomains);

        $newDomainsRows = array_map(function($domain){
            return [$domain, time(), time()];
        }, $newDomains);

        $newDomainsInsertedCount = Yii::$app->db->createCommand()
            ->batchInsert($this->table, [
                $this->dbFields['domains_column'], $this->dbFields['updated_column'], $this->dbFields['created_column']
            ], $newDomainsRows)
            ->execute();

        return $newDomainsInsertedCount;
    }

    /**
     * Get panel info by panel host name
     * @param $hostName
     * @return mixed
     * @throws Exception
     */
    public function getPanelInfo($hostName)
    {
        $curlOptions = [
            CURLOPT_HTTPHEADER => [
                "Host: $hostName",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
            ],
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $hostName,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $firstError = curl_error($ch);
            curl_close($ch);

            throw new Exception($firstError);
        }

        $info = curl_getinfo($ch);

        curl_close($ch);

        return [
            'content' => $content,
            'info' => $info,
        ];
    }


    public function getPanelStatus($hostName)
    {
        try {
            $panelData = $this->getPanelInfo($hostName);
        } catch (Exception $e) {
            $status = [
                'status' => self::PANEL_STATUS_NOT_RESOLVED,
                'error' => $e,
            ];

            return $status;
        }

        $panelInfo = $panelData['info'];
        $panelContent = $panelData['content'];
        $panelIp = $panelInfo['primary_ip'];

        // Levopanel active or frozen
        if ($panelIp === $this->panelIp) {

            // If All checks passed => Frozen
            $frozenChecks = [];
            foreach (static::$_inactivePanelSigns as $sign) {
                $frozenChecks[] = boolval(stripos($panelContent, $sign));
            }

            if (array_product($frozenChecks) === 1) {
                return [
                    'status' => self::PANEL_STATUS_FROZEN,
                    'info' => $panelInfo,
                ];
            }

            // If All checks passed => Active
            $activeChecks = [];
            foreach (static::$_activePanelSigns as $sign) {
                $activeChecks[] = boolval(stripos($panelContent, $sign));
            }

            if (array_product($activeChecks) === 1) {
                return [
                    'status' => self::PANEL_STATUS_ACTIVE,
                    'info' => $panelInfo,
                ];
            }
        }

        // Perfectpanel
        if ($panelIp === self::PERFECTPANEL_IP) {
            return [
                'status' => self::PANEL_STATUS_PERFECTPANEL,
                'info' => $panelInfo,
            ];
        }

        // Not Levopanel & not Perfectpanel
        if ($panelIp !== $this->panelIp && $panelIp !== self::PERFECTPANEL_IP) {
            return [
                'status' => self::PANEL_STATUS_IP_NOT_LEVOPANEL,
                'info' => $panelInfo,
            ];
        }


        // Parking page
        $matchCountsRule = static::$_parkingPageSign['match_counts'];
        $matchCounts = substr_count(strtolower($panelContent), strtolower($matchCountsRule['needle']));

        if ($matchCounts == $matchCountsRule['counts']) {
            return [
                'status' => self::PANEL_STATUS_PARKING,
                'info' => $panelInfo,
            ];
        }

        // Other
        return [
            'status' => self::PANEL_STATUS_OTHER,
            'info' => $panelInfo,
        ];
    }




    public function check()
    {
        $foundNeighbors = $this->searchPanelNeighbors($this->panelHostName);

        if ($foundNeighbors) {
            $this->updatePanelDomainsList($foundNeighbors);
        }

        $panelNeighbors = $this->getPanelNeighbors();
        
        foreach ($panelNeighbors as $panel) {

            $panelStatus = $this->getPanelStatus($panel['domain']);

            error_log(print_r($panelStatus, 1),0);

        }

        return true;
    }

    /**
     * Make request to tcpiputils.com API endpoint
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function request($params = array())
    {
        $baseRequestParams = [
            'apikey' => $this->apiKey,
            'version' => $this->apiVersion,
        ];

        $request = http_build_query($baseRequestParams + $params);

        $curlOptions = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::API_URL . "?$request",
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $firstError = curl_error($ch);
            curl_close($ch);

            throw new Exception("Curl initialisation error: $firstError");
        }

        curl_close($ch);

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }

}