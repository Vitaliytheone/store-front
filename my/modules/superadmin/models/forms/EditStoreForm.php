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
    public $move_domain;

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
            [['move_domain'], 'safe'],
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
     * @throws \yii\db\Exception
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $isChangedCustomer = $this->_store->customer_id == $this->customer_id ? false : true;
        $oldCustomerId = $this->_store->customer_id;
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
            SslCert::updateAll(['cid' => $this->customer_id], [
                'pid' => $this->_store->id,
                'project_type' => SslCert::PROJECT_TYPE_STORE,
                'cid' => $oldCustomerId,
            ]);

            if ((bool)$this->move_domain) {
                Domains::updateAll(['customer_id' => $this->customer_id], [
                    'domain' => $this->_store->domain,
                    'customer_id' => $oldCustomerId,
                ]);
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
            'move_domain' => Yii::t('app/superadmin', 'stores.modal.edit.move_domain'),
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
