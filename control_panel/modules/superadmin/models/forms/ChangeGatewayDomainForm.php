<?php

namespace superadmin\models\forms;


use common\helpers\DnsHelper;
use common\helpers\SuperTaskHelper;
use common\models\gateways\Sites;
use control_panel\helpers\DomainsHelper;
use yii\base\Model;
use Yii;

/**
 * Class ChangeGatewayDomainForm
 * @package superadmin\models\forms
 */
class ChangeGatewayDomainForm extends Model
{
    public $domain;
    public $subdomain;

    /** @var Sites */
    private $gateway;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], 'required'],
            ['domain', 'string'],
            ['domain', 'trim'],
            [['subdomain'], 'safe'],
        ];
    }

    /**
     * Set gateway
     * @param Sites $site
     */
    public function setGateway(Sites $site)
    {
        $this->gateway = $site;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     * @throws \yii\base\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $oldSubdomain = $this->gateway->subdomain;
        $oldDomain = $this->gateway->domain;

        $domain = $this->prepareDomain();

        $isChangedDomain = $oldDomain != $domain;
        $isChangedSubdomain = $oldSubdomain != $this->subdomain;

        if (!$isChangedDomain && !$isChangedSubdomain) {
            return true;
        }

        if ($isChangedSubdomain) {
            $this->gateway->subdomain = $this->subdomain;
        }

        if ($isChangedDomain) {
            if (!$this->gateway->disableDomain()) {
                $this->addError('domain', Yii::t('app/superadmin', 'gateways.change_domain.error'));
                return false;
            }

            $this->gateway->domain = $domain;
        }

        $this->gateway->dns_status = Sites::DNS_STATUS_ALIEN;
        $this->gateway->dns_checked_at = null;

        if (!$this->gateway->save(false)) {
            $this->addError('domain', Yii::t('app/superadmin', 'gateways.change_domain.error'));
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением
        if ($isChangedDomain) {
            $this->gateway->refresh();

            $this->gateway->ssl = 0;

            SuperTaskHelper::setTasksNginx($this->gateway);

            $this->gateway->enableDomain();
            $this->gateway->renameDb();
            $this->gateway->save(false);
        }

        if ($isChangedSubdomain) {
            if ($this->subdomain) {
                DnsHelper::removeDns($this->gateway);
            } else {
                DnsHelper::addMainDns($this->gateway);
            }
        }

        return true;
    }

    /**
     * Prepare domain
     * @return string
     */
    public function prepareDomain()
    {
        $domain = trim(strtolower(DomainsHelper::idnToAscii($this->domain)));

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain' => Yii::t('app/superadmin', 'gateways.change_domain.domain'),
            'subdomain' => Yii::t('app/superadmin', 'gateways.change_domain.subdomain'),
        ];
    }
}