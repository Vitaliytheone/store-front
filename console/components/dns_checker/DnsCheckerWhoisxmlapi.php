<?php

namespace console\components\dns_checker;

use common\models\panels\Params;
use common\helpers\CurlHelper;
use my\helpers\DomainsHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class DnsWhoisxmlapiChecker
 * @package console\components\dns_checker
 */
class DnsCheckerWhoisxmlapi extends DnsCheckerBase
{
    /**
     * Who is lookup data
     * @var array
     */
    protected $who_is_lookup = [];
    /**
     * Who is nameservers data
     * @var array
     */
    protected $who_is_nameservers = [];

    /**
     * Set ns-lookup data
     * @param array $lookupData
     */
    public function setLookup(array $lookupData)
    {
        $this->who_is_lookup = $lookupData;
    }

    /**
     * Get ns-lookup data
     * @return array
     */
    public function getLookup() : array
    {
        return $this->who_is_lookup;
    }

    /**
     * Set nameservers data
     * @param array $nameserversData
     */
    public function setNameservers(array $nameserversData)
    {
        $this->who_is_nameservers = $nameserversData;
    }

    /**
     * Get nameservers data
     * @return array
     */
    public function getNameservers() : array
    {
        return $this->who_is_nameservers;
    }

    /** @inheritdoc */
    public function check()
    {
        $requestParams = http_build_query([
            'apiKey' => Params::get(Params::CATEGORY_SERVICE, Params::CODE_WHOISXMLAPI, 'ssl'),
            'domainName' => $this->getDomain(),
            'outputFormat' => 'JSON',
        ]);

        $request = Yii::$app->params['whoisxmlapi']['api_url'] . '/?' . $requestParams;

        $response = CurlHelper::request($request);

        if (!$response) {
            return false;
        }

        $response = json_decode($response, true);

        if (json_last_error()) {
            $this->addError('json_last_error', json_last_error_msg());

            return false;
        }

        $this->setLookup($response);

        if (!ArrayHelper::getValue($response, 'WhoisRecord')) {
            $this->addError('who_is_record', 'Invalid api NsLoockup response!');

            return false;
        }

        // Check 2 places
        $whoisNameservers = ArrayHelper::getValue(
            $response,
            ['WhoisRecord', 'registryData', 'nameServers', 'hostNames'],
            ArrayHelper::getValue($response, ['WhoisRecord', 'nameServers', 'hostNames'], [])
        );

        if (!$whoisNameservers || !is_array($whoisNameservers)) {
            $this->addError('who_is_nameservers', 'Domain nameservers are not defined!');

            return false;
        }

        $this->setNameservers($whoisNameservers);

        $configNameservers = array_values(array_filter(Yii::$app->params[DomainsHelper::getRegistrarName($this->getDomain()).'.my.ns'], function($nameserver) { return !empty($nameserver); }));

        if (array_udiff($configNameservers, $whoisNameservers, 'strcasecmp')) {

            $this->addError('match_nameservers','Nameservers do not match!');

            return false;
        }

        return true;
    }
}