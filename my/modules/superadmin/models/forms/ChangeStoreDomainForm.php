<?php
namespace my\modules\superadmin\models\forms;

use Yii;
use common\models\stores\Stores;
use my\helpers\DnsHelper;
use my\helpers\DomainsHelper;
use my\helpers\SuperTaskHelper;
use yii\base\Model;

/**
 * Class ChangeStoreDomainForm
 * @package my\modules\superadmin\models\forms
 */
class ChangeStoreDomainForm extends Model {

    public $domain;
    public $subdomain;

    /**
     * @var Stores
     */
    private $_store;

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
     * Set store
     * @param Stores $store
     */
    public function setProject(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Save domain
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $oldSubdomain = $this->_store->subdomain;
        $oldDomain = $this->_store->domain;

        $domain = $this->prepareDomain();

        $isChangedDomain = $oldDomain != $domain;
        $isChangedSubdomain = $oldSubdomain != $this->subdomain;

        if (!$isChangedDomain && !$isChangedSubdomain) {
            return true;
        }

        if ($isChangedSubdomain) {
            $this->_store->subdomain = $this->subdomain;
        }

        if ($isChangedDomain) {
            if (!$this->_store->disableDomain()) {
                $this->addError('domain', 'Can not change domain');
                return false;
            }

            $this->_store->domain = $domain;
        }

        if (!$this->_store->save(false)) {
            $this->addError('domain', 'Can not change domain');
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением
        if ($isChangedDomain) {
            $this->_store->refresh();

            SuperTaskHelper::setTasksNginx($this->_store);

            $this->_store->enableDomain();
            $this->_store->renameDb();
            $this->_store->save(false);
        }

        if ($isChangedSubdomain) {
            if ($this->subdomain) {
                // Если выделен и project.subdomain = 0, удаляем домен из cloudns и новый не создаем, меняем project.subdomain = 1.
                DnsHelper::removeMainDns($this->_project);
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
            'domain' => Yii::t('app/superadmin', 'stores.change_domain.column_domain'),
            'subdomain' => Yii::t('app/superadmin', 'stores.change_domain.column_subdomain')
        ];
    }
}