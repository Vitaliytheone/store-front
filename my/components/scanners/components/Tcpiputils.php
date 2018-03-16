<?php

namespace my\components\scanners\components;

use yii\helpers\ArrayHelper;
use yii\base\Component;
use yii\base\Exception;

class Tcpiputils extends Component
{
    const API_METHOD_DOMAIN_NEIGHBORS = 'domainneighbors';
    const API_METHOD_NS_NEIGHBORS = 'nsneighbors';

    const API_RESPONSE_STATUS_SUCCESS = 'succeed';
    const API_RESPONSE_STATUS_ERROR = 'error';

    const NS_NEIGHBORS_DOMAINS_PER_PAGE = 2500;

    /** @var string Api key */
    public $apiKey;

    /** @var string Api version */
    public $apiVersion = '1.0';

    /** @var string Api url */
    public $apiUrl = 'https://www.utlsapi.com/api.php';

    /**
     * Return panel neighbors domains list from API
     * @param string $hostName
     * @return array
     * @throws Exception
     */
    public function getDomainNeighbors($hostName)
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
     * Returns domains sharing the same name server (NS record)
     * @param $nsName
     * @param int $page
     * @return array
     * @throws Exception
     */
    public function getNSNeighbors($nsName, $page = 1)
    {
        $requestParams = [
            'type' => self::API_METHOD_NS_NEIGHBORS,
            'q' => $nsName,
            'page' => $page,
        ];

        $response = $this->request($requestParams);

        $error = self::API_RESPONSE_STATUS_ERROR === strtolower(ArrayHelper::getValue($response,'status'));

        if ($error) {
            throw new Exception("Tcpiputils.com API response error! " . "Response: " . json_encode($response) . " Request: " . json_encode($requestParams));
        }

        $page = ArrayHelper::getValue($response, 'data.question.page');
        $domainsTotal = ArrayHelper::getValue($response, 'data.ndomains');
        $nameServer = ArrayHelper::getValue($response, 'data.question.nameserver');
        $domains = ArrayHelper::getValue($response, 'data.domains');
        $pagesTotal = ceil($domainsTotal / static::NS_NEIGHBORS_DOMAINS_PER_PAGE);

        return [
            'page' => $page,
            'pagesTotal' => $pagesTotal,
            'nameserver' => $nameServer,
            'domainsTotal' => $domainsTotal,
            'domains' => $domains,
        ];
    }

    /**
     * Make request to tcpiputils.com API endpoint
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function request($params = array())
    {
        if (empty($this->apiKey) || empty($this->apiUrl) || empty($this->apiVersion)) {
            throw new Exception('Bad Api config! Missing required data: apiKey or apiUrl or apiVersion!');
        }

        $baseRequestParams = [
            'apikey' => $this->apiKey,
            'version' => $this->apiVersion,
        ];

        $request = http_build_query($baseRequestParams + $params);

        $curlOptions = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->apiUrl . "?$request",
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