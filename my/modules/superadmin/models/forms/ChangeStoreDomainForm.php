<?php
namespace superadmin\models\forms;

use Yii;
use common\models\stores\Stores;
use sommerce\helpers\DnsHelper;
use my\helpers\DomainsHelper;
use common\helpers\SuperTaskHelper;
use yii\base\Model;

/**
 * Class ChangeStoreDomainForm
 * @package superadmin\models\forms
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
    public function setStore(Stores $store)
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

        if (!$this->_store->disableDomain()) {
            $this->addError('domain', Yii::t('app/superadmin', 'stores.modal.error_change_domain'));
            return false;
        }

        $this->_store->domain = $domain;
        $this->_store->subdomain = $this->subdomain;

        if (!$this->_store->save(false)) {
            $this->addError('domain', Yii::t('app/superadmin', 'stores.modal.error_change_domain'));
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением

        $this->_store->refresh();

        $this->_store->enableDomain();

        if ($isChangedDomain) {
            $this->_store->renameDb();
        }

        $this->_store->save(false);

        SuperTaskHelper::setTasksNginx($this->_store);

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