<?php

namespace control_panel\models\forms;

use common\helpers\SuperTaskHelper;
use common\models\sommerces\StoreDomains;
use common\models\sommerces\Stores;
use control_panel\components\validators\StoreDomainValidator;
use common\models\sommerces\Auth;
use Yii;
use yii\base\Model;

/**
 * Class EditStoreDomainForm
 * @package control_panel\models\forms
 */
class EditStoreDomainForm extends Model
{
    public $domain;

    public $preparedDomain;

    /**
     * @var Stores
     */
    public $_store;

    /**
     * @var Auth
     */
    public $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], StoreDomainValidator::class],
            [['domain'], 'required', 'message' => Yii::t('app', 'error.store.invalid_domain')],
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
     * Get store
     * @return  Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Set user
     * @param Auth $user
     */
    public function setUser(Auth $user)
    {
        $this->_user = $user;
    }

    /**
     * Get current user
     * @return Auth
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Edit store domain method
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $oldDomain = null;
        $domain = $this->preparedDomain;

        $storeDomain = StoreDomains::findOne([
            'store_id' => $this->_store->id,
            'type' => StoreDomains::DOMAIN_TYPE_DEFAULT
        ]);

        if ($storeDomain) {
            $oldDomain = $storeDomain->domain;
        }

        $isChangedDomain = $oldDomain != $domain;

        if (!$isChangedDomain) {
            return true;
        }

        if (!$this->_store->disableDomain()) {
            $this->addError('domain', Yii::t('app', 'error.store.can_not_change_domain'));
            return false;
        }

        $this->_store->domain = $domain;
        $this->_store->subdomain = 0;
        $this->_store->dns_status = 0;

        if (!$this->_store->save(false)) {
            $this->addError('domain', Yii::t('app', 'error.store.can_not_change_domain'));
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением
        $this->_store->refresh();

        $this->_store->enableDomain();
        $this->_store->renameDb();
        $this->_store->save(false);

        SuperTaskHelper::setTasksNginx($this->_store);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain' => Yii::t('app', 'stores.edit_store_domain.domain'),
        ];
    }
}