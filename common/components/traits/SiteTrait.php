<?php

namespace common\components\traits;

use common\helpers\DbHelper;
use my\helpers\DomainsHelper;
use Yii;

/**
 * Class UnixTimeFormatTrait
 * @package common\components\traits
 */
trait SiteTrait {

    public function getSubdomain()
    {
        $domain = $this->site;
        $subPrefix = str_replace('.', '-', $domain);
        $panelDomainName = Yii::$app->params['panelDomain'];
        $subDomain = $subPrefix . '.' . $panelDomainName;

        return $subDomain;
    }

    /**
     * Create store db name
     */
    public function generateDbName()
    {
        $domain = Yii::$app->params['panelDomain'];

        $baseDbName = static::DB_NAME_PREFIX . $this->id . "_" . strtolower(str_replace([$domain, '.', '-'], '', DomainsHelper::idnToAscii($this->getDomain())));

        $postfix = null;

        do {
            $dbName = $baseDbName .  ($postfix ? '_' . $postfix : '');
            $postfix ++;
        } while(DbHelper::existDatabase($dbName));

        $this->setDbName($dbName);
    }
}