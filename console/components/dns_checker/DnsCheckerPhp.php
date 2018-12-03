<?php

namespace console\components\dns_checker;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class DnsRecordsChecker
 * @package console\components\dns_checker
 */
class DnsCheckerPhp extends DnsCheckerBase
{
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
     * Set domain/subdomain dns records
     * @param array $dnsRecords
     */
    public function setDnsRecords(array $dnsRecords) {
        $this->_dns_records = $dnsRecords;
    }

    /**
     * Get domain/subdomain dns records
     * @return array
     */
    public function getDnsRecords() : array
    {
        return $this->_dns_records;
    }

    /**
     * Set domain/subdomain dns checkout record
     * @param array $dnsRecord
     */
    public function setDnsCheckoutRecord(array $dnsRecord) {
        $this->_dns_checkout_record = $dnsRecord;
    }

    /**
     * Get domain/subdomain dns checkout record
     * @return array
     */
    public function getDnsCheckoutRecord() : array
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

        $this->setDnsRecords(dns_get_record($this->getDomain()));

        if ($this->getSubdomain()) {
            // Check Subdomain
            $dnsCNAME = dns_get_record($this->getDomain(), DNS_CNAME);

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
            $dnsA = dns_get_record($this->getDomain(), DNS_A);

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