<?php

namespace superadmin\models\forms;

use common\models\sommerces\SuperAdmin;
use yii\base\Model;
use Yii;

/**
 * PasswordUpdateForm
 */
class PasswordUpdateForm extends Model
{
    public $current_password;
    public $password;
    public $password_repeat;

    /**
     * @var SuperAdmin
     */
    private $_admin;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['current_password', 'password', 'password_repeat'], 'required'],

            ['current_password', function ($attribute, $params) {
                if ($this->hasErrors($attribute)) {
                    return false;
                }
                if (!$this->_admin->validatePassword($this->$attribute)) {
                    $this->addError($attribute, 'Wrong current password.');
                    return false;
                }

                return true;
            }],

            ['password', 'compare'],
        ];
    }

    /**
     * Set admin user
     * @param SuperAdmin $admin
     */
    public function setUser(SuperAdmin $admin)
    {
        $this->_admin = $admin;
    }

    /**
     * Labels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'current_password' => Yii::t('app/superadmin', 'account.current_password'),
            'password' => Yii::t('app/superadmin', 'account.new_password'),
            'password_repeat' => Yii::t('app/superadmin', 'account.confirm_password'),
        ];
    }

    /**
     * Save new super admin password
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_admin->setPassword($this->password);

        if (!$this->_admin->save(true, ['password'])) {
            $this->addErrors($this->_admin->getErrors());
            return false;
        }

        return true;
    }
}
