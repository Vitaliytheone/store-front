<?php

namespace superadmin\models\forms;

use Yii;
use common\models\panels\SuperAdmin;
use yii\base\Model;

/**
 * EditStaffForm is the model behind the Edit Staff form.
 */
class EditStaffForm extends Model
{
    public $username;
    public $first_name;
    public $last_name;
    public $status;
    public $access = [];

    /**
     * @var SuperAdmin
     */
    private $_superAdmin;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'status'], 'required'],
            [['first_name', 'last_name'], 'string', 'max' => 250],
            ['username', 'uniqueUsername'],
            [['status'], 'in', 'range' => array_keys(SuperAdmin::getStatuses())],
            ['access', 'safe']
        ];
    }

    /**
     * Set super admin
     * @param SuperAdmin $staff
     */
    public function setStaff(SuperAdmin $staff)
    {
        $this->_superAdmin = $staff;
    }

    /**
     * Save admin settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_superAdmin->username = $this->username;
        $this->_superAdmin->first_name = $this->first_name;
        $this->_superAdmin->last_name = $this->last_name;
        $this->_superAdmin->status = $this->status;
        $this->_superAdmin->setSommerceAccessRules($this->access);

        if (!$this->_superAdmin->save()) {
            $this->addErrors($this->_superAdmin->getErrors());
            return false;
        }

        return true;
    }

    /**
     * Validate username
     * @param $attribute
     * @return bool
     */
    public function uniqueUsername($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        if ((SuperAdmin::find()->where("{$attribute} = :{$attribute} AND id <> :id", [
            ":{$attribute}" => $this->{$attribute},
            ':id' => $this->_superAdmin->id,
        ])->one())) {
            $this->addError($attribute, Yii::t('yii', '{attribute} "{value}" has already been taken.', [
                'attribute' => $this->getAttributeLabel($attribute),
                'value' => $this->{$attribute}
            ]));
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
            'username' => Yii::t('app/superadmin', 'staff.edit_staff.account'),
            'first_name' => Yii::t('app/superadmin', 'staff.edit_staff.first_name'),
            'last_name' => Yii::t('app/superadmin', 'staff.edit_staff.last_name'),
            'status' => Yii::t('app/superadmin', 'staff.edit_staff.status'),
            'access' => Yii::t('app/superadmin', 'staff.edit_staff.access_rules'),
        ];
    }
}
