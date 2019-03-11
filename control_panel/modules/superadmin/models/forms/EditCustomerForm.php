<?php

namespace superadmin\models\forms;

use common\models\sommerces\Customers;
use yii\base\Model;
use Yii;

/**
 * EditCustomerForm is the model behind the Edit Customer form.
 */
class EditCustomerForm extends Model
{
    public $email;

    /**
     * @var Customers
     */
    private $_customer;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }

    /**
     * Set customer
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Save customer settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_customer->email = $this->email;
        $this->_customer->scenario = Customers::SCENARIO_UPDATE;

        if (!$this->_customer->save()) {
            $this->addErrors($this->_customer->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app/superadmin', 'customers.edit.email_label'),
        ];
    }
}
