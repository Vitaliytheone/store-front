<?php
namespace console\components\panelchecker;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PanelcheckerComponent
 * @property string table
 * @package console\components\panelchecker
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

    public $proxy = [
        'ip',
        'port',
        'type' => CURLPROXY_HTTP,
    ];
 
    public $panelHostName = 'levopanel.com';
    public $panelNetworks = [
        '147.135.223.128',
        '104.*.*.*',
    ];

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

    public static $statuses = [
        self::PANEL_STATUS_ACTIVE => 'Active',
        self::PANEL_STATUS_FROZEN => 'Disabled',
        self::PANEL_STATUS_PERFECTPANEL => 'Perfect',
        self::PANEL_STATUS_NOT_RESOLVED => 'Fail',
        self::PANEL_STATUS_IP_NOT_LEVOPANEL => 'Moved',
        self::PANEL_STATUS_PARKING => 'Moved',
        self::PANEL_STATUS_OTHER => 'Error',
    ];

    /**
     * Return Status Name by status value
     * @param $status
     * @return mixed
     */
    public static function getStatusName($status){
        return ArrayHelper::getValue(static::$statuses, $status, $status);
    }

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
                'id',
                $this->dbFields['domains_column'],
                $this->dbFields['status_column'],
                $this->dbFields['ip_column'],
                $this->dbFields['details_column'],
                $this->dbFields['updated_column'],
                $this->dbFields['created_column'],
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
                $this->dbFields['domains_column'],
                $this->dbFields['updated_column'],
                $this->dbFields['created_column']
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

        $proxyOptions = [];

        if (isset($this->proxy['ip'])) {
            $proxyOptions = [
                CURLOPT_PROXYTYPE => ArrayHelper::getValue($this->proxy, 'type', null),
                CURLOPT_PROXY => $this->proxy['ip'] . ':' . $this->proxy['port'],
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions + $proxyOptions);

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

    /**
     * Get panel status by panel info data
     * @param $hostName
     * @return array
     */
    public function getPanelStatus($hostName)
    {
        try {
            $panelData = $this->getPanelInfo($hostName);
        } catch (Exception $e) {

            $status = [
                'status' => self::PANEL_STATUS_NOT_RESOLVED,
                'info' => $e->getMessage(),
            ];

            return $status;
        }

        $panelInfo = $panelData['info'];
        $panelContent = $panelData['content'];
        $panelIp = $panelInfo['primary_ip'];

        // Levopanel active or frozen

        if (static::matchNetworks($panelIp, $this->panelNetworks)) {

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
        if ($panelIp !== self::PERFECTPANEL_IP) {
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

    /**
     * Match ip address to network by mask
     *
     * matchIp('10.168.1.90', '10.168.1.0-10.168.1.100');
     * matchIp('10.168.1.90', '10.168.*.*');
     * matchIp('10.168.1.90', '10.168.0.0/16');
     * matchIp('10.168.1.90', '10.169.1.0/24');
     * matchIp('10.168.1.90', '10.168.1.90');
     *
     * @param $ip
     * @param $network
     * @return bool
     */
    public static function matchNetwork($ip, $network)
    {
            $network=trim($network);
            $ip = trim($ip);
            if ($ip == $network) {
                return true;
            }
            $network = str_replace(' ', '', $network);
            if (strpos($network, '*') !== false) {
                if (strpos($network, '/') !== false) {
                    $asParts = explode('/', $network);
                    $network = @ $asParts[0];
                }
                $nCount = substr_count($network, '*');
                $network = str_replace('*', '0', $network);
                if ($nCount == 1) {
                    $network .= '/24';
                } else if ($nCount == 2) {
                    $network .= '/16';
                } else if ($nCount == 3) {
                    $network .= '/8';
                } else if ($nCount > 3) {
                    return true; // if *.*.*.*, then all, so matched
                }
            }

            $d = strpos($network, '-');
            if ($d === false) {
                $ip_arr = explode('/', $network);

                if (!isset($ip_arr[1])) {
                    return false;
                }

                if (!preg_match("@\d*\.\d*\.\d*\.\d*@", $ip_arr[0], $matches)){
                    $ip_arr[0].=".0";    // Alternate form 194.1.4/24
                }

                $network_long = ip2long($ip_arr[0]);
                $x = ip2long($ip_arr[1]);
                $mask = long2ip($x) == $ip_arr[1] ? $x : (0xffffffff << (32 - $ip_arr[1]));
                $ip_long = ip2long($ip);
                return ($ip_long & $mask) == ($network_long & $mask);
            } else {
                $from = trim(ip2long(substr($network, 0, $d)));
                $to = trim(ip2long(substr($network, $d+1)));
                $ip = ip2long($ip);
                return ($ip>=$from and $ip<=$to);
            }
    }

    /**
     * Match ip address one of networks
     * @param $ip
     * @param array $networks
     * @return bool
     */
    public static function matchNetworks($ip, array $networks){

        echo ($ip) . PHP_EOL;

        foreach ($networks as $network) {
            if (static::matchNetwork($ip, $network)) {
                return true;
                break;
            }
        }
        return false;
    }

    /**
     * Check all neighbors panels
     * @return array
     */
    public function check()
    {
        $foundNeighbors = $this->searchPanelNeighbors($this->panelHostName);

        if ($foundNeighbors) {
            $this->updatePanelDomainsList($foundNeighbors);
        }

        $panelNeighbors = $this->getPanelNeighbors();

        $panelImploded = null;
        foreach ($panelNeighbors as $panel) {

            $panelStatus = $this->getPanelStatus($panel['domain']);

            $id = $panel['id'];
            $status = ArrayHelper::getValue($panelStatus, 'status', null);
            $ip = '';
            $details = json_encode(ArrayHelper::getValue($panelStatus, 'info', null));
            $time = time();

            if ($status !== self::PANEL_STATUS_NOT_RESOLVED) {
                $ip = ArrayHelper::getValue($panelStatus, 'info.primary_ip');
            }

            $panelImploded = ($panelImploded ? $panelImploded . ',' : '') . "('$id', '$status', '$ip', '$details', '$time')";
        }

        $statusColumn = $this->dbFields['status_column'];
        $ipColumn = $this->dbFields['ip_column'];
        $detailsColumn = $this->dbFields['details_column'];
        $updatedColumn = $this->dbFields['updated_column'];

        $command = Yii::$app->db
            ->createCommand("
                INSERT INTO $this->table (id, $statusColumn, $ipColumn, $detailsColumn, $updatedColumn)
                VALUES $panelImploded
                ON DUPLICATE KEY UPDATE
                $statusColumn = VALUES($statusColumn), $ipColumn = VALUES($ipColumn), $detailsColumn = VALUES($detailsColumn), $updatedColumn = VALUES($updatedColumn)
                
        ")->execute();

        return $panelNeighbors;
    }

    /**
     * Make request to tcpiputils.com API endpoint
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function request($params = array())
    {
        if (!isset($this->apiKey)) {
            throw new Exception('Bad Api config! Missing required data: apiKey!');
        }

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