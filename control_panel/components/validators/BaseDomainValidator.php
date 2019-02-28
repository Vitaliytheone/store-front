<?php
namespace control_panel\components\validators;

use common\helpers\CurlHelper;
use control_panel\helpers\DomainsHelper;
use common\models\sommerces\OrderLogs;
use Yii;
use yii\validators\Validator;

/**
 * Class BaseDomainValidator
 * @package control_panel\components\validators
 */
class BaseDomainValidator extends Validator
{
    protected $domain;

    protected $user_id;

    /**
     * @return bool
     */
    protected function isValidDomainZone()
    {
        if (!empty(Yii::$app->params['my.domains.stop_zones'])) {
            if (preg_match('/(\.' . implode("|", Yii::$app->params['my.domains.stop_zones']). '$)/ui', $this->domain)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isValidDomainName()
    {
        if (!empty(Yii::$app->params['my.domains.stop_words'])) {
            if (preg_match('/(' . implode("|", Yii::$app->params['my.domains.stop_words']). ')/ui', $this->domain)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check is valid domain
     * @param $domainName
     * @return array
     */
    protected function isExistDomainName($domainName)
    {
        $result = array('result' => false);

        $list = CurlHelper::request('https://www.whoisxmlapi.com/whoisserver/WhoisService?cmd=GET_DN_AVAILABILITY&domainName=' . $domainName . '&username=' . Yii::$app->params['dnsLogin'] . '&password=' . Yii::$app->params['dnsPasswd'] . '&getMode=DNS_AND_WHOIS&outputFormat=JSON');
        $listEncode = json_decode($list);
        if ($listEncode !== false) {
            if (!empty($listEncode->DomainInfo) && $listEncode->DomainInfo->domainAvailability == 'UNAVAILABLE') {
                $result = array('result' => true, 'domain' => $listEncode->DomainInfo->domainName);
            }
        } else {
            $result = array('result' => true, 'domain' => $domainName);
        }

        $OrderLogsModel = new OrderLogs();

        $OrderLogsModel->domain = $domainName;
        $OrderLogsModel->cid = $this->user_id;
        $OrderLogsModel->date = time();
        $OrderLogsModel->log = json_encode(array('result' => $result, 'html' => $list));
        $OrderLogsModel->save();

        return $result;
    }

    /**
     * Prepare domain
     * @return string
     */
    protected function prepareDomain()
    {
        $domain = trim(mb_strtolower($this->domain));
        $domain = DomainsHelper::idnToAscii($domain);

        $exp = explode("://", $domain);

        if (count($exp) > 1) {
            $domain = $exp['1'];
        }

        $exp = explode("/", $domain);

        $domain = $exp['0'];

        if (substr($domain, 0, 4) == 'www.') {
            $domain = substr($domain, 4);
        }

        return $domain;
    }
}