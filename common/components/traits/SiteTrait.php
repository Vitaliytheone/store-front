<?php

namespace common\components\traits;

use common\helpers\DbHelper;
use common\models\common\ProjectInterface;
use my\helpers\DomainsHelper;
use Yii;

/**
 * Class UnixTimeFormatTrait
 * @package common\components\traits
 */
trait SiteTrait {

    /**
     * @return string
     */
    public function getSubdomain()
    {
        $domain = $this->getDomain();
        $domain = preg_replace("/\.com$/uis", "", $domain);
        $subPrefix = str_replace('.', '-', $domain);
        $subDomain = $subPrefix . '.' . $this->getMainDomain();

        return $subDomain;
    }

    /**
     * Create store db name
     */
    public function generateDbName()
    {
        $baseDbName = $this->getDbNamePrefix() . "_" . strtolower(str_replace([$this->getMainDomain(), '.', '-'], '', DomainsHelper::idnToAscii($this->getDomain())));

        $postfix = null;

        do {
            $dbName = $baseDbName .  ($postfix ? '_' . $postfix : '');
            $postfix ++;
        } while(DbHelper::existDatabase($dbName));

        $this->setDbName($dbName);
    }

    /**
     * Get site
     * @return string
     */
    public function getSite()
    {
        return DomainsHelper::idnToUtf8($this->getDomain());
    }

    /**
     * Get site url
     * @return string
     */
    public function getSiteUrl()
    {
        return ($this->ssl ? 'https://' : 'http://') . $this->getSite();
    }

    /**
     * @inheritdoc
     */
    public function getBaseDomain()
    {
        return $this->getSite();
    }

    /**
     * @inheritdoc
     */
    public function setSslMode($isActive)
    {
        $this->ssl = $isActive;
    }

    /**
     * @inheritdoc
     */
    public function getBaseSite()
    {
        return ($this->ssl == ProjectInterface::SSL_MODE_ON ? 'https://' : 'http://') . $this->getBaseDomain();
    }
}