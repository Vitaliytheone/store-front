<?php
namespace superadmin\models\forms;

use common\models\panels\Domains;
use common\helpers\DnsHelper;
use my\helpers\DomainsHelper;
use common\helpers\SuperTaskHelper;
use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use my\helpers\ProvidersHelper;
use yii\base\Model;
use Yii;

/**
 * Class ChangeDomainForm
 * @package superadmin\models\forms
 */
class ChangeDomainForm extends Model {

    public $domain;
    public $subdomain;

    /**
     * @var Project
     */
    private $_project;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], 'required'],
            [['subdomain'], 'safe'],
        ];
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
    }

    /**
     * Save domain
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $oldSubdomain = $this->_project->subdomain;
        $oldDomain = $this->_project->site;

        $domain = $this->prepareDomain();

        $isChangedDomain = $oldDomain != $domain;
        $isChangedSubdomain = $oldSubdomain != $this->subdomain;

        if (!$isChangedDomain && !$isChangedSubdomain) {
            return true;
        }

        if ($isChangedSubdomain) {
            $this->_project->subdomain = $this->subdomain;
        }

        if ($isChangedDomain) {
            if (!$this->_project->disableDomain(true)) {
                $this->addError('domain', Yii::t('app/superadmin', 'panels.change_domain.error'));
                return false;
            }

            ProvidersHelper::makeProvidersOld($domain);

            if (($additionalService = AdditionalServices::findOne([
                'name' => $oldDomain
            ]))) {

                $additionalService->name = $domain;
                $additionalService->generateApiHelp($domain);

                if (!$additionalService->save(false)) {
                    $this->addError('domain', Yii::t('app/superadmin', 'panels.change_domain.error'));
                    return false;
                }
            }

            $this->_project->site = $domain;
        }

        $this->_project->dns_status = Project::DNS_STATUS_ALIEN;
        $this->_project->dns_checked_at = null;

        if (!$this->_project->save(false)) {
            $this->addError('domain', Yii::t('app/superadmin', 'panels.change_domain.error'));
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением
        if ($isChangedDomain) {
            $this->_project->refresh();

            $this->_project->ssl = 0;

            SuperTaskHelper::setTasksNginx($this->_project);

            $this->_project->enableDomain();
            $this->_project->renameDb();
            $this->_project->save(false);
        }

        if ($isChangedSubdomain) {
            if ($this->subdomain) {
                // Если выделен и project.subdomain = 0, удаляем домен из cloudns и новый не создаем, меняем project.subdomain = 1.
                $domain = Domains::findOne(['domain' => $this->_project->site]);

                if (!isset($domain)) {
                    DnsHelper::removeDns($this->_project);
                }
            } else {
                // Если он не выделен и project.subdomain = 1 старый домен не удаляем, новый домен создаем в cloudns и ставим project.subdomain = 0.
                DnsHelper::addMainDns($this->_project);
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
            'domain' => Yii::t('app/superadmin', 'panels.change_domain.domain'),
            'subdomain' => Yii::t('app/superadmin', 'panels.change_domain.subdomain'),
        ];
    }
}