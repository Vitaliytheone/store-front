<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\Customers;
use yii\base\Model;

/**
 * Class CustomerPasswordForm
 * @package my\modules\superadmin\models\forms
 */
class CustomerPasswordForm extends Model
{
    public $password;

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
            [['password'], 'required'],
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

        $this->_customer->setPassword($this->password);

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
            'password' => 'Password'
        ];
    }
}
