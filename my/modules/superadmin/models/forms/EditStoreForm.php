<?php

namespace my\modules\superadmin\models\forms;

use Yii;
use yii\base\Model;
use common\models\stores\Stores;
use common\models\panels\Customers;
use sommerce\helpers\ConfigHelper;

/**
 * Class EditStoreForm
 * @package my\modules\superadmin\models\forms
 */
class EditStoreForm extends Model
{
    public $name;
    public $customer_id;
    public $currency;

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
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_store->customer_id = $this->customer_id;
        $this->_store->name = $this->name;
        $this->_store->currency = $this->currency;

        if (!$this->_store->save()) {
            $this->addErrors($this->_store->getErrors());
            return false;
        }

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
