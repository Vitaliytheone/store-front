<?php
namespace my\models\forms;

use common\helpers\SuperTaskHelper;
use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\components\validators\StoreDomainValidator;
use Yii;
use yii\base\Model;

/**
 * Class EditStoreDomainForm
 * @package my\models\forms
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
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['domain'], StoreDomainValidator::class],
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
            $this->addError('domain', 'Can update domain');
            return false;
        }

        $this->_store->domain = $domain;

        if (!$this->_store->save(false)) {
            $this->addError('domain', 'Can update domain');
            return false;
        }

        // Если был изменен домен, то необходимо провести еще операции с БД, рестартом нгинкса, добавлением
        $this->_store->refresh();

        SuperTaskHelper::setTasksNginx($this->_store);

        $this->_store->enableDomain();
        $this->_store->renameDb();
        $this->_store->save(false);

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