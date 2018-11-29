<?php

namespace console\components\dns_checker;

use yii\base\Exception;
use yii\base\Model;

/**
 * Class DnsCheckerBase
 * @package console\components\crons
 */
abstract class DnsCheckerBase extends Model
{
    /**
     * Domain for check
     * @var  string
     */
    protected $_domain;

    /**
     * Is domain is subdomain
     * @var bool
     */
    protected $_subdomain = false;

    /**
     * Set domain/subdomain
     * @param string $domain
     * @throws Exception
     */
    public function setDomain(string $domain)
    {
        $this->_domain = trim($domain);
    }

    /**
     * Return domain/subdomain
     * @return string
     * @throws Exception
     */
    public function getDomain() : string
    {
        if (!$this->_domain) {
            throw new Exception('Define domain first!');
        }

        return $this->_domain;
    }

    /**
     * Set is domain is subdomain
     * @param bool $subdomain
     */
    public function setSubdomain(bool $subdomain)
    {
        $this->_subdomain = $subdomain;
    }

    /**
     * Get is domain is subdomain
     * @return bool
     */
    public function getSubdomain()
    {
        return $this->_subdomain;
    }

    /**
     * Run cron task
     * @return boolean
     */
    abstract function check();
}