<?php

namespace superadmin\models\forms;

use common\models\panels\SuperAdmin;
use Yii;
use yii\base\Model;

/**
 * ChangeStaffPasswordForm is the model behind the Change Password form.
 */
class ChangeStaffPasswordForm extends Model
{
    public $password;

    /**
     * @var SuperAdmin
     */
    private $_superadmin;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password'], 'required'],
            [['password'], 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Set superadmin
     * @param SuperAdmin $superadmin
     */
    public function setAdmin(SuperAdmin $superadmin)
    {
        $this->_superadmin = $superadmin;
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

        $this->_superadmin->setPassword($this->password);

        if (!$this->_superadmin->save(false)) {
            $this->addErrors($this->_superadmin->getErrors());
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
            'password' => Yii::t('app/superadmin', 'staff.change_password.new_password'),
        ];
    }
}
