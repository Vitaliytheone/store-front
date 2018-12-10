<?php

namespace console\components\dns_checker;

use common\models\common\ProjectInterface;
use common\models\panels\Project;
use common\models\stores\Stores;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class DnsRecordsChecker
 * @package console\components\dns_checker
 */
class DnsCheckerPhp extends DnsCheckerBase
{
    /**
     * Project
     * @var Stores|Project
     */
    protected $_project;

    /**
     * Domain/subdomain dns records data
     * @var
     */
    protected $_dns_records = [];

    /**
     * Domain/subdomain dns checkout record
     * @var array
     */
    protected $_dns_checkout_record = [];

    /**
     * Flush or not dns cache
     * @var bool
     */
    protected $_flush_dns_cache = true;

    /**
     * Set project
     * @param $project Project|Stores
     */
    public function setProject($project)
    {
        $this->_project = $project;
    }

    /**
     * Get project
     * @return Project|Stores
     */
    public function getProject()
    {
        return $this->_project;
    }

    /**
     * Set domain/subdomain dns records
     * @param array|mixed $dnsRecords
     */
    public function setDnsRecords($dnsRecords) {
        $this->_dns_records = $dnsRecords;
    }

    /**
     * Get domain/subdomain dns records
     * @return array|mixed
     */
    public function getDnsRecords()
    {
        return $this->_dns_records;
    }

    /**
     * Set domain/subdomain dns checkout record
     * @param array|mixed $dnsRecord
     */
    public function setDnsCheckoutRecord($dnsRecord) {
        $this->_dns_checkout_record = $dnsRecord;
    }

    /**
     * Get domain/subdomain dns checkout record
     * @return array|mixed
     */
    public function getDnsCheckoutRecord()
    {
        return $this->_dns_checkout_record;
    }

    /**
     * Set flush cache
     * @param $flush
     */
    public function setFlushCache(bool $flush)
    {
        $this->_flush_dns_cache = $flush;
    }

    /**
     * Get flush cache
     * @return bool
     */
    public function getFlushCache() : bool
    {
        return $this->_flush_dns_cache;
    }

    /** @inheritdoc */
    public function check()
    {
        if ($this->getFlushCache()) {
            // Reset local dns cache
            exec('rndc flush', $output, $returnVar);
        }

        // TODO:: remove @-error_control operand after php-bug "A temporary server error" is fixed
        $dnsRecords = @dns_get_record($this->getDomain());

        if (!is_array($dnsRecords)) {
            return false;
        }

        $this->setDnsRecords($dnsRecords);

        if ($this->getSubdomain()) {
            // Check Subdomain
            $dnsCNAME = @dns_get_record($this->getDomain(), DNS_CNAME);

            if (!$dnsCNAME || !is_array($dnsCNAME)) {
                return false;
            }

            $dnsCNAME = reset($dnsCNAME);
            $dnsValidCNAME = Yii::$app->params['dns.checker.records']['CNAME'];

            $this->setDnsCheckoutRecord($dnsCNAME);

            if (ArrayHelper::getValue($dnsCNAME, 'target') !== $dnsValidCNAME['target']) {

                $this->addError('match_subdomain_domain_cname_record', 'Subdomain CNAME does not match expected CNAME');

                return false;
            }

        }  else {
            // Check Domain
            $dnsA = @dns_get_record($this->getDomain(), DNS_A);

            if (!$dnsA || !is_array($dnsA)) {
                return false;
            }

            $dnsA = reset($dnsA);
            $dnsValidA = Yii::$app->params['dns.checker.records']['A'];

            $this->setDnsCheckoutRecord($dnsA);

            if (ArrayHelper::getValue($dnsA, 'ip') !== $dnsValidA['ip']) {

                $this->addError('match_domain_domain_a_record', 'Domain A record does not match expected A record');

                return false;
            }
        }

        return true;
    }
}