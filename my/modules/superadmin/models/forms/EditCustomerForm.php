<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\Customers;
use yii\base\Model;

/**
 * EditCustomerForm is the model behind the Edit Customer form.
 */
class EditCustomerForm extends Model
{
    public $email;
    public $first_name;
    public $last_name;

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
            [['email', 'last_name', 'first_name'], 'required'],
            [['email'], 'email'],
            [['first_name', 'last_name'], 'string', 'max' => 250],
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
        $this->_customer->first_name = $this->first_name;
        $this->_customer->last_name = $this->last_name;


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
            'email' => 'Email',
            'first_name' => 'First name',
            'last_name' => 'Last name',
        ];
    }
}
