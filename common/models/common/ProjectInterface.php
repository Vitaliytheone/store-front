<?php
namespace common\models\common;

/**
 * Interface ProjectInterface
 * @package common\models\common
 */
interface ProjectInterface
{
    const PROJECT_TYPE_PANEL = 1;
    const PROJECT_TYPE_STORE = 2;

    const SSL_MODE_ON = 1;
    const SSL_MODE_OFF = 0;

    const DNS_STATUS_NOT_DEFINED = null;
    const DNS_STATUS_ALIEN = 0;
    const DNS_STATUS_MINE = 1;

    /**
     * Return project type
     * @return integer
     */
    public static function getProjectType();

    /**
     * Return current base domain of project (only raw domain, without http scheme, itc)
     * @return string|null
     */
    public function getBaseDomain();

    /**
     * Return current domain base site of project (http:// or  https:// scheme included)
     * @return mixed
     */
    public function getBaseSite();

    /**
     * Set is SSL-mode active for base domain
     * @param $isActive bool
     */
    public function setSslMode($isActive);

    /**
     * Terminate project
     * @return bool
     */
    public function terminate();

    /**
     * Restore project
     * @return mixed
     */
    public function restore();

    /**
     * Set whois_lookup
     * @param array $whoisLookupData
     */
    public function setWhoisLookup(array $whoisLookupData);

    /**
     * Get whois_lookup
     * @return array
     */
    public function getWhoisLookup();

    /**
     * Set nameservers
     * @param array $nameserversList
     */
    public function setNameservers(array $nameserversList);

    /**
     * Get nameservers
     * @return array
     */
    public function getNameservers();
}