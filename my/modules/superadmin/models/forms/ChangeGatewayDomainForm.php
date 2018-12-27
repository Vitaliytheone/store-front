<?php

namespace superadmin\models\forms;


use common\helpers\DnsHelper;
use common\models\gateways\Sites;
use yii\base\Model;
use Yii;

/**
 * Class ChangeGatewayDomainForm
 * @package superadmin\models\forms
 */
class ChangeGatewayDomainForm extends Model
{
    public $domain;

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
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        DnsHelper::removeDns($this->gateway);

        $this->gateway->domain = $this->domain;

        if (!$this->gateway->save(false)) {
            $this->addError('domain', Yii::t('app/superadmin', 'gateways.change_domain.error'));
            return false;
        }

        return true;
    }
}