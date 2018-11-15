<?php
namespace superadmin\models\forms;

use Yii;
use common\models\panels\SuperAdmin;
use yii\base\Model;

/**
 * CreateStaffForm is the model behind the Create Staff form.
 */
class CreateStaffForm extends Model
{
    public $username;
    public $first_name;
    public $last_name;
    public $status;
    public $password;
    public $access = [];


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'status', 'password'], 'required'],
            [['password'], 'string', 'min' => 6],
            [['first_name', 'last_name'], 'string', 'max' => 250],
            ['username', 'uniqueUsername'],
            [['status'], 'in', 'range' => array_keys(SuperAdmin::getStatuses())],
            ['access', 'safe']
        ];
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

        $model = new SuperAdmin();
        $model->username = $this->username;
        $model->setPassword($this->password);
        $model->first_name = $this->first_name;
        $model->last_name = $this->last_name;
        $model->status = $this->status;
        $model->setAccessRules($this->access);
        $model->generateAuthKey();

        if (!$model->save()) {
            $this->addErrors($model->getErrors());
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

        if ((SuperAdmin::findOne([
            $attribute => $this->{$attribute},
        ]))) {
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
            'password' => Yii::t('app/superadmin', 'staff.create_staff.password'),
            'first_name' => Yii::t('app/superadmin', 'staff.edit_staff.first_name'),
            'last_name' => Yii::t('app/superadmin', 'staff.edit_staff.last_name'),
            'status' => Yii::t('app/superadmin', 'staff.edit_staff.status'),
            'access' => Yii::t('app/superadmin', 'staff.edit_staff.access_rules'),
        ];
    }
}
