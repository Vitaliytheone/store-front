<?php

namespace superadmin\models\forms;

use common\models\panels\Domains;
use common\helpers\DnsHelper;
use common\models\panels\PanelDomains;
use control_panel\helpers\DomainsHelper;
use common\helpers\SuperTaskHelper;
use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use control_panel\helpers\ProvidersHelper;
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
    private $project;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], 'required'],
            ['domain', 'validateDomain'],
            [['subdomain'], 'safe'],
        ];
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
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

        $oldSubdomain = $this->project->subdomain;
        $oldDomain = $this->project->site;
        $isForeignDomain = PanelDomains::find()->where([
            'panel_id' => $this->project,
            'type' => PanelDomains::TYPE_FOREIGN_SUBDOMAIN
        ])->exists();
        $this->project->setForeignSubdomain($isForeignDomain);

        $domain = $this->prepareDomain();

        $isChangedDomain = $oldDomain != $domain;
        $isChangedSubdomain = $oldSubdomain != $this->subdomain;

        if (!$isChangedDomain && !$isChangedSubdomain) {
            return true;
        }

        if ($isChangedSubdomain) {
            $this->project->subdomain = $this->subdomain;
        }

        if ($isChangedDomain) {
            if (!$this->project->disableDomain(true)) {
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

            $this->project->site = $domain;
        }

        $this->project->dns_status = Project::DNS_STATUS_ALIEN;
        $this->project->dns_checked_at = null;

        if (!$this->project->save(false)) {
            $this->addError('domain', Yii::t('app/superadmin', 'panels.change_domain.error'));
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением
        if ($isChangedDomain) {
            $this->project->refresh();

            $this->project->ssl = 0;

            SuperTaskHelper::setTasksNginx($this->project);

            $this->project->enableDomain();
            $this->project->renameDb();
            $this->project->save(false);
        }

        if ($isChangedSubdomain) {
            if ($this->subdomain) {
                // Если выделен и project.subdomain = 0, удаляем домен из cloudns и новый не создаем, меняем project.subdomain = 1.
                $domain = Domains::findOne(['domain' => $this->project->site]);

                if (!isset($domain) && !$isForeignDomain) {
                    DnsHelper::removeDns($this->project);
                }
            } elseif (!$isForeignDomain) {
                // Если он не выделен и project.subdomain = 1 старый домен не удаляем, новый домен создаем в cloudns и ставим project.subdomain = 0.
                DnsHelper::addMainDns($this->project);
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

    /**
     * @param $attribute
     * @param $params
     */
    public function validateDomain($attribute, $params)
    {
        $domain = $this->prepareDomain();

        if ($this->project->site != $domain) {
            $panelExist = Project::find()
                ->where(['site' => $domain, 'act' => [Project::STATUS_ACTIVE, Project::STATUS_FROZEN]])
                ->exists();

            if ($panelExist) {
                $this->addError($attribute, Yii::t('app/superadmin', 'panels.change_domain.error_exist'));
            }
        }
    }
}
