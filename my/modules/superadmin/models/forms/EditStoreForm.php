<?php

namespace superadmin\models\forms;

use common\models\panels\Domains;
use common\models\panels\SslCert;
use Yii;
use yii\base\Model;
use common\models\stores\Stores;
use common\models\panels\Customers;
use sommerce\helpers\ConfigHelper;

/**
 * Class EditStoreForm
 * @package superadmin\models\forms
 */
class EditStoreForm extends Model
{
    public $name;
    public $customer_id;
    public $currency;
    public $moveDomain;

    /**
     * @var Stores
     */
    private $_store;

    /**
     * @inheritdoc
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['moveDomain'], 'safe'],
            [['name', 'customer_id', 'currency'], 'required'],
            ['name', 'string', 'max' => 255],
            ['customer_id', 'in', 'range' => $this->setAllowableValueRange()],
            ['currency', 'in', 'range' => array_keys(ConfigHelper::getCurrenciesList())],
        ];
    }

    /**
     * Set the range of customer_id for validation rules
     * @return array
     */
    private function setAllowableValueRange()
    {
        $query = Customers::find()->all();
        $range = [];
        foreach ($query as $customer) {
            $range[] = $customer->id;
        }
        return $range;
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
     * Save store
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $isChangedCustomer = $this->_store->customer_id == $this->customer_id ? false : true;
        $transaction = Yii::$app->db->beginTransaction();

        $this->_store->customer_id = $this->customer_id;
        $this->_store->name = $this->name;
        $this->_store->currency = $this->currency;

        if (!$this->_store->save()) {
            $transaction->rollBack();
            $this->addErrors($this->_store->getErrors());
            return false;
        }

        if ($isChangedCustomer) {
            $ssl = SslCert::findOne(['pid' => $this->_store->id, 'project_type' => SslCert::PROJECT_TYPE_STORE]);
            if ($ssl) {
                $ssl->cid = $this->customer_id;
                $ssl->update(false);
            }

            if ((bool)$this->moveDomain) {
                $domain = Domains::findOne(['domain' => $this->_store->domain]);
                if ($domain) {
                    $domain->customer_id = $this->customer_id;
                    $domain->update(false);
                }
            }
        }
        $transaction->commit();

        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => Yii::t('app/superadmin', 'stores.list.column_customer'),
            'name' => Yii::t('app/superadmin', 'stores.list.column_name'),
            'currency' => Yii::t('app/superadmin', 'stores.list.column_currency'),
            'moveDomain' => Yii::t('app/superadmin', 'stores.modal.edit.move_domain'),
        ];
    }

    /**
     * Get active customers
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCustomers()
    {
        return Customers::find()->andWhere([
            'status' => Customers::STATUS_ACTIVE
        ])->limit(10)->all();
    }
}
