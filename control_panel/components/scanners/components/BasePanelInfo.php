<?php

namespace control_panel\components\scanners\components;

use common\models\panels\SuperToolsScanner;
use DOMDocument;
use yii\helpers\ArrayHelper;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class BasePanelInfo
 * @package control_panel\components\scanners\components
 */
abstract class BasePanelInfo extends Component
{
    const HTTP_STATUS_200 = 200;

    // Applied rules
    const RULE_STATUS_ACTIVE        = 'status_active';
    const RULE_STATUS_DISABLED      = 'status_disabled';
    const RULE_STATUS_PERFECTPANEL  = 'status_perfectpanel';
    const RULE_STATUS_MOVED         = 'status_moved';

    public $rules = [
        'status_active' => true,
        'status_disabled' => true,
        'status_perfectpanel' => true,
        'status_moved' => true,
    ];

    /**
     * Curl proxy settings
     * @var array
     */
     public $proxy = [
        'ip' => null,
        'port' => null,
        'type' => CURLPROXY_HTTP,
    ];

    /**
     * Curl timeouts settings
     * @var array
     */
    public $timeouts = [
        'timeout' => 20,
        'connection_timeout' => 10,
    ];

    /** @var string curl user agent header */
    public $curlUseragent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";

    /**
     * Current requested host curl get info & content data
     * @var
     */
    protected $currentPanelData = [];

    /**
     * Current panels info set
     * Array of:
     * [
     *   'host' => $host,
     *   'content' => $content,
     *   'info' => $info,
     * ]
     * @var array
     */
    protected $panelsData = [];

    /**
     * Return if panel checking rule is set
     * @param string $ruleName
     * @return mixed
     */
    private function _isRuleActive($ruleName)
    {
        return ArrayHelper::getValue($this->rules, $ruleName, false);
    }

    /**
     * Check if panel Active
     * @return bool
     */
    abstract protected function checkStatusActive();

    /**
     * Check if requested host frozen
     * @return bool
     */
    abstract protected function checkStatusDisabled();

    /**
     * Check if requested host Perfect panel
     * @return bool
     * @throws Exception
     */
    protected function checkStatusPerfectPanel()
    {
        $host = parse_url(ArrayHelper::getValue($this->currentPanelData, 'info.url'), PHP_URL_HOST);

        if (!$host) {
            return false;
        }

        $panelData = $this->getUrlInfo($host . '/admin');

        $content = ArrayHelper::getValue($panelData, 'content');

        if (empty($content) || ArrayHelper::getValue($panelData, 'info.http_code') != self::HTTP_STATUS_200) {
            return false;
        }
        new domDocument();

        return boolval(stripos($content, '<!--Hello,_world!-->'));
    }

    /**
     * Return panel status by panel info data
     * @param $url
     * @return array
     * @throws Exception
     */
    public function getPanelInfo($url)
    {
        $this->currentPanelData = $this->getUrlInfo($url);

        return $this->_getPanelInfo();
    }

    /**
     * Return panel statuses by panels info data
     * @param array $urls
     * @return array
     * @throws Exception
     */
    public function getPanelsInfo(array $urls)
    {
        $panelsStatuses = [];

        $panelsData = $this->getUrlsInfo($urls);


        foreach ($panelsData as $panelData) {

            $this->currentPanelData = $panelData;
            $panelsStatuses[] = $this->_getPanelInfo();
        }

        return $panelsStatuses;
    }

    /**
     * Return panel status by panel data
     * @return array
     * @throws Exception
     */
    protected function _getPanelInfo()
    {
        if (!isset($this->currentPanelData)) {
            throw new Exception('No panel data! Check panel domain first!');
        }

        $panelInfo = [
            'host' => $this->currentPanelData['host'],
            'status' => null,
            'info' => null,
        ];

        if (
            isset($this->currentPanelData['error'])
        ) {
            return array_merge($panelInfo, [
                'status' => SuperToolsScanner::PANEL_STATUS_MOVED,
                'info' => $this->currentPanelData['error'],
            ]);
        }

        if (
            $this->_isRuleActive(static::RULE_STATUS_ACTIVE) &&
            $this->checkStatusActive()
        ) {
            return array_merge($panelInfo, [
                'status' => SuperToolsScanner::PANEL_STATUS_ACTIVE,
                'info' => $this->currentPanelData['info'],
            ]);
        }

        if (
            $this->_isRuleActive(static::RULE_STATUS_DISABLED) &&
            $this->checkStatusDisabled()
        ) {
            return array_merge($panelInfo, [
                'status' => SuperToolsScanner::PANEL_STATUS_DISABLED,
                'info' => $this->currentPanelData['info'],
            ]);
        }

        if (
            $this->_isRuleActive(static::RULE_STATUS_PERFECTPANEL) &&
            $this->checkStatusPerfectPanel()
        ) {
            return array_merge($panelInfo, [
                'status' => SuperToolsScanner::PANEL_STATUS_PERFECTPANEL,
                'info' => $this->currentPanelData['info'],
            ]);
        }

        //  All other features
        return array_merge($panelInfo, [
            'status' => SuperToolsScanner::PANEL_STATUS_MOVED,
            'info' => $this->currentPanelData['info'],
        ]);
    }

    /**
     * Get panel info by panel host name
     * @param $url
     * @return mixed
     */
    public function getUrlInfo($url)
    {
        $host = parse_url($url, PHP_URL_SCHEME) ? parse_url($url, PHP_URL_HOST) : parse_url('http://' . $url, PHP_URL_HOST);

        $curlOptions = [
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => ArrayHelper::getValue($this->timeouts, 'timeout', 20),
            CURLOPT_CONNECTTIMEOUT => ArrayHelper::getValue($this->timeouts, 'connection_timeout', 10),
            CURLOPT_HTTPHEADER => [
                "Host:" . $host,
                "User-Agent:" . $this->curlUseragent,
            ],
            CURLOPT_URL => $url,
        ];

        $proxyOptions = [
            CURLOPT_PROXYTYPE => $this->proxy['type'],
            CURLOPT_PROXY => $this->proxy['ip'] . ':' . $this->proxy['port'],
        ];

        if (isset($this->proxy['ip'])) {
            $curlOptions += $proxyOptions;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $firstError = curl_error($ch);
            curl_close($ch);

            return [
                'host' => $host,
                'error' => $firstError,
            ];
        }

        $info = curl_getinfo($ch);



        curl_close($ch);


        return [
            'host' => $host,
            'content' => $content,
            'info' => $info,
        ];
    }

    /**
     * Return info data for urls list
     * @param array $urls
     * @return array
     */
    public function getUrlsInfo(array $urls)
    {
        $connectionHandlers = [];

        $mh = curl_multi_init();

        /** Build batch request data */
        foreach ($urls as $url) {

            $host = parse_url($url, PHP_URL_SCHEME) ? parse_url($url, PHP_URL_HOST) : parse_url('http://' . $url, PHP_URL_HOST);

            if (!$host) continue;

            $curlOptions = [

                CURLOPT_TIMEOUT => ArrayHelper::getValue($this->timeouts, 'timeout', 20),
                CURLOPT_CONNECTTIMEOUT => ArrayHelper::getValue($this->timeouts, 'connection_timeout', 10),

                CURLOPT_HTTPHEADER => [
                    "Host: " . $host,
                    "User-Agent: " . $this->curlUseragent,
                ],
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_VERBOSE => false,
                CURLOPT_URL => $url,

                CURLOPT_PRIVATE => $host,
            ];


            $proxyOptions = [
                CURLOPT_PROXYTYPE => $this->proxy['type'],
                CURLOPT_PROXY => $this->proxy['ip'] . ':' . $this->proxy['port'],
            ];

            if (isset($this->proxy['ip'])) {
                $curlOptions +=  $proxyOptions;
            }

            $ch = curl_init();
            curl_setopt_array($ch, $curlOptions);
            curl_multi_add_handle($mh, $ch);

            $connectionHandlers[] = $ch;
        }

        echo "before request sent\n";
        /** Do batch request */
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        echo "request sent\n";

        /** Process results */

        $panelsData = [];

        foreach ($connectionHandlers as $ch)
        {
            $host = curl_getinfo($ch, CURLINFO_PRIVATE);


            // System Errors
            if (curl_errno($ch)) {

                $panelsData[] = [
                    'host' => $host,
                    'error' => curl_error($ch),
                ];

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            // Json decode errors
            if ((json_last_error() !== JSON_ERROR_NONE)) {

                $panelsData[] = [
                    'host' => $host,
                    'error' => json_last_error(),
                ];

                curl_multi_remove_handle($mh, $ch);
                continue;
            }

            $panelsData[] = [
                'host' => $host,
                'content' => curl_multi_getcontent($ch),
                'info' => curl_getinfo($ch),
            ];

            curl_multi_remove_handle($mh, $ch);
            continue;
        }

        curl_multi_close($mh);


        echo "return curl result\n";

        return $panelsData;
    }
}